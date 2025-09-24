<script setup lang="ts">
import { debounce } from 'lodash';
import { ref, watch } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import useNotify from '@/Composables/useNotify.js';
import { useConfirm } from "primevue/useconfirm";
import AppLayout from '@/Layouts/AppLayout.vue';
import { generateSlug } from '@/utils/slugify.js';
import formatDate from '@/utils/formatDate';
// state
const props = defineProps({
    data: Object,
    filters: Object
})

const notify = useNotify()
const confirm = useConfirm();
const loading = ref(false)

const modal = ref({
    create: false,
    edit: false,
});

const formCreate = useForm({
    name: '',
    slug: '',
    logo: null,
    is_show: true,
    is_pin: true,
});

const formEdit = useForm({
    id: null,
    name: '',
    slug: '',
    logo: null,
    is_show: false,
    is_pin: true,

});

const search = ref(props.filters.search || '');
const lazyParams = ref({
    first: (props.data.current_page - 1) * props.data.per_page,
    rows: props.data.per_page,
    page: props.data.current_page - 1,
    sortField: props.filters.sortField || 'created_at',
    sortOrder: props.filters.sortOrder ?? -1,
});

const fetchCategories = debounce((page = 1) => {
    loading.value = true;
    router.get(route('admin.product.brands'), {
        search: search.value,
        page: page,
        per_page: lazyParams.value.rows,
        sortField: lazyParams.value.sortField,
        sortOrder: lazyParams.value.sortOrder,
    }, {
        preserveState: true,
        replace: true,
        preserveScroll: true,
        onFinish: () => {
            loading.value = false;
        }
    });
}, 500);

watch(search, () => {
    fetchCategories(1);
});

function onPage(event) {
    lazyParams.value = { ...lazyParams.value, ...event };
    fetchCategories(event.page + 1);
}

function onSort(event) {
    lazyParams.value.sortField = event.sortField;
    lazyParams.value.sortOrder = event.sortOrder;
    fetchCategories(lazyParams.value.page + 1);
}
const handleFileChange = (event, action) => {
    const file = event.target.files[0];
    if (file) {
        if (action == 'create') {
            formCreate.logo = file;
        } else {
            formEdit.logo = file;
        }
    }

}

// get data
const openModalEdit = ({ id, name, slug, is_show }) => {
    Object.assign(formEdit, { id, name, slug, is_show: !!is_show })
    modal.value.edit = true
}

// call api
const create = () => {
    formCreate.post(route('admin.product.brands.store'), {
        onSuccess: () => {
            formCreate.reset();
            modal.value.create = false;
            notify.success('Thêm thương hiệu thành công');
        },
        onError: (errors) => {
            Object.values(errors).forEach(errorArray => {
                notify.error(errorArray)
            });
        }
    })
}
const update = () => {
    formEdit.post(route('admin.product.brands.update', formEdit.id), {
        onSuccess: () => {
            notify.success('Cập nhật thương hiệu thành công');
            modal.value.edit = false;
        },
        onError: (errors) => {
            Object.values(errors).forEach(errorArray => {
                notify.error(errorArray)
            });
        }
    })
}

const deleteBrands = (data) => {
    confirm.require({
        message: 'Bạn có chắc muốn xóa thương hiệu này?',
        header: 'Xác nhận xoá',
        acceptLabel: 'Xoá',
        rejectLabel: 'Huỷ',
        acceptClass: 'p-button-danger',
        accept: () => {
            router.delete(route('admin.product.brands.destroy', data.id), {
                onSuccess: () => {
                    notify.success('Xóa thương hiệu thành công')
                    fetchCategories(lazyParams.value.page + 1)
                },
                onError: (errors) => {
                    Object.values(errors).forEach(errorArray => {
                        notify.error(errorArray)
                    });
                }
            })
        },
        reject: () => {
            // Không làm gì cả
        }
    })
}
</script>

<template>

    <Head title="Quản lý  thương hiệu" />
    <AppLayout>
        <div class="card">
            <DataTable :value="data.data" dataKey="id" stripedRows responsiveLayout="scroll" :paginator="true"
                :rows="lazyParams.rows" :first="lazyParams.first" :totalRecords="data.total" :loading="loading"
                :rowsPerPageOptions="[5, 10, 25, 50]" :lazy="true" @page="onPage" @sort="onSort">
                <template #header>
                    <div class="flex flex-wrap gap-2 mb-5 items-center justify-between">
                        <h2 class="m-0 text-2xl font-sans">Quản lý thương hiệu</h2>
                        <div class="flex gap-2 justify-center">
                            <InputText v-model="search" placeholder="Tìm kiếm..." />
                            <Button icon="pi pi-plus" label="Thêm mới" @click="modal.create = true" />
                        </div>
                    </div>
                </template>
                <Column field="stt" header="STT">
                    <template #body="{ index }">
                        <span>{{ index + 1 + lazyParams.first }}</span>
                    </template>
                </Column>
                <Column field="logo" header="Ảnh thương hiệu">
                    <template #body="{ data }">
                        <Image preview :src="data.logo" alt="" class="w-16 h-16 object-cover" />
                    </template>
                </Column>
                <Column field="name" header="Tên thương hiệu">
                    <template #body="{ data }">
                        <span>{{ data.name }}</span>
                    </template>
                </Column>
                <Column field="slug" header="Slug">
                    <template #body="{ data }">
                        <span>{{ data.slug }}</span>
                    </template>
                </Column>
                <Column field="is_show" header="Trạng thái">
                    <template #body="{ data }">
                        <Tag :severity="data.is_show ? 'primary' : 'danger'">
                            {{ data.is_show ? 'Hiển thị' : 'Ẩn' }}</Tag>
                    </template>
                </Column>
                <Column field="is_pin" header="Ghim">
                    <template #body="{ data }">
                        <Tag :severity="data.is_pin == 1 ? 'primary' : 'danger'">
                            {{ data.is_pin == 1 ? 'Có' : 'Không' }}</Tag>
                    </template>
                </Column>
                <Column field="created_at" header="Ngày tạo" sortable>
                    <template #body="{ data }">
                        <span>{{ formatDate(data.created_at) }}</span>
                    </template>
                </Column>
                <Column :exportable="false" header="Thao tác">
                    <template #body="{ data }">
                        <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="openModalEdit(data)" />
                        <Button icon="pi pi-trash" outlined rounded severity="danger" @click="deleteBrands(data)" />
                    </template>
                </Column>
                <!-- Empty State -->
                <template #empty>
                    <div class="text-center py-2 text-gray-500">
                        Chưa có dữ liệu
                    </div>
                </template>
            </DataTable>
        </div>
        <!-- modal create -->
        <Dialog v-model:visible="modal.create" modal header="Thêm thương hiệu" :style="{ width: '30rem' }">
            <form action="" @submit.prevent="create">
                <div>
                    <label for="name" class="block text-sm mb-2 font-medium text-surface-700 dark:text-surface-200">Tên
                        thương hiệu*</label>
                    <InputText id="name" type="text" class="mt-1 block w-full" placeholder="Nhập tên thương hiệu"
                        v-model="formCreate.name" @input="formCreate.slug = generateSlug(formCreate.name)" required
                        autocomplete="name" />
                </div>
                <div class="mt-4">
                    <label for="slug" class="block text-sm mb-2 font-medium text-surface-700 dark:text-surface-200">Slug
                        (<i>Không cần nhập</i>)</label>
                    <InputText id="slug" type="text" class="mt-1 block w-full" v-model="formCreate.slug"
                        autocomplete="slug" placeholder="" @input="formCreate.slug = generateSlug(formCreate.slug)" />
                </div>
                <div class="mt-4">
                    <label for="logo" class="block text-sm mb-2 font-medium text-surface-700 dark:text-surface-200">Ảnh
                        đại
                        diện (<i>Không nhất thiết phải thêm</i>)</label>
                    <InputText id="logo" @change="handleFileChange($event, 'create')" type="file"
                        class="mt-1 block w-full" />
                </div>
                <div class="mt-4">
                    <label class="flex items-center cursor-pointer ">
                        <Checkbox v-model="formCreate.is_show" binary class="mr-2" />
                        <span>Hiển thị </span>
                    </label>
                    <label class="flex items-center cursor-pointer mt-2">
                        <Checkbox v-model="formCreate.is_pin" binary class="mr-2" />
                        <span>Ghim </span>
                    </label>
                </div>
                <div class="flex justify-end mt-5 gap-2">
                    <Button type="button" label="Hủy" severity="secondary" @click="modal.create = false"></Button>
                    <Button type="submit" label="Lưu"></Button>
                </div>
            </form>

        </Dialog>
        <!-- modal edit -->
        <Dialog v-model:visible="modal.edit" modal header="Cập nhật thương hiệu" :style="{ width: '30rem' }">
            <form action="" @submit.prevent="update">
                <div>
                    <label for="title" class="block text-sm mb-2 font-medium text-surface-700 dark:text-surface-200">Tên
                        thương
                        hiệu</label>
                    <InputText id="title" type="text" class="mt-1 block w-full" placeholder="Nhập tiêu đề"
                        v-model="formEdit.name" @input="formEdit.slug = generateSlug(formEdit.name)" required
                        autocomplete="title" />
                </div>
                <div class="mt-4">
                    <label for="slug"
                        class="block text-sm mb-2 font-medium text-surface-700 dark:text-surface-200">Slug</label>
                    <InputText id="slug" type="text" class="mt-1 block w-full" v-model="formEdit.slug"
                        autocomplete="slug" placeholder="Nhập slug"
                        @input="formEdit.slug = generateSlug(formEdit.slug)" />
                </div>
                <div class="mt-4">
                    <label for="logo" class="block text-sm mb-2 font-medium text-surface-700 dark:text-surface-200">Ảnh
                        đại
                        diện (<i>Không nhất thiết phải thêm</i>)</label>
                    <InputText id="logo" @change="handleFileChange($event, 'edit')" type="file"
                        class="mt-1 block w-full" />
                </div>
                <div class="mt-4">
                    <label class="flex items-center cursor-pointer ">
                        <Checkbox v-model="formEdit.is_show" binary class="mr-2" />
                        <span>Hiển thị </span>
                    </label>
                    <label class="flex items-center cursor-pointer mt-2">
                        <Checkbox v-model="formEdit.is_pin" binary class="mr-2" />
                        <span>Ghim </span>
                    </label>
                </div>
                <div class="flex justify-end mt-5 gap-2">
                    <Button type="button" label="Hủy" severity="secondary" @click="modal.edit = false"></Button>
                    <Button type="submit" label="Lưu"></Button>
                </div>
            </form>
        </Dialog>
    </AppLayout>
</template>
<style>
.p-select {
    width: 100%;
}

.p-paginator-content {
    flex-wrap: nowrap !important;
}
</style>