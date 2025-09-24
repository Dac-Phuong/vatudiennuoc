<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import useNotify from '@/Composables/useNotify.js';
import { useConfirm } from "primevue/useconfirm";
import AppLayout from '@/Layouts/AppLayout.vue';
import formatDate from '@/utils/formatDate';
import { debounce } from 'lodash';
import { ref, watch } from 'vue';
import { generateSlug } from '@/utils/slugify.js';
// state
const props = defineProps({
    data: Object,
    filters: Object,
    categories: Array
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
    is_show: true,
    parent_id: null,
});

const formEdit = useForm({
    id: null,
    name: '',
    slug: '',
    is_show: false,
    parent_id: null,
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
    router.get(route('admin.product.categories'), {
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

// get data
const openModalEdit = ({ id, name, slug, is_show, parent_id }) => {
    Object.assign(formEdit, { id, name, slug, is_show: !!is_show, parent_id });
    modal.value.edit = true
}

// call api
const create = () => {
    formCreate.post(route('admin.product.categories.store'), {
        onSuccess: () => {
            formCreate.reset();
            modal.value.create = false;
            notify.success('Thêm danh mục thành công');
        },
        onError: (errors) => {
            Object.values(errors).forEach(errorArray => {
                notify.error(errorArray)
            });
        }
    })
}
const update = () => {
    formEdit.post(route('admin.product.categories.update', formEdit.id), {
        onSuccess: () => {
            notify.success('Cập nhật danh mục thành công');
            modal.value.edit = false;
        },
        onError: (errors) => {
            Object.values(errors).forEach(errorArray => {
                notify.error(errorArray)
            });
        }
    })
}

const deleteCategory = (data) => {
    confirm.require({
        message: 'Bạn có chắc muốn xóa danh mục này?',
        header: 'Xác nhận xoá',
        acceptLabel: 'Xoá',
        rejectLabel: 'Huỷ',
        acceptClass: 'p-button-danger',
        accept: () => {
            router.delete(route('admin.product.categories.destroy', data.id), {
                onSuccess: () => {
                    notify.success('Xóa danh mục thành công')
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

    <Head title="Quản lý danh mục Sản phẩm" />
    <AppLayout>
        <div class="card">
            <DataTable :value="data.data" dataKey="id" stripedRows responsiveLayout="scroll" :paginator="true"
                :rows="lazyParams.rows" :first="lazyParams.first" :totalRecords="data.total" :loading="loading"
                :rowsPerPageOptions="[5, 10, 25, 50]" :lazy="true" @page="onPage" @sort="onSort">
                <template #header>
                    <div class="flex flex-wrap gap-2 mb-5 items-center justify-between">
                        <h2 class="m-0 text-2xl font-sans">Quản lý danh mục Sản phẩm</h2>
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
                <Column field="name" header="Tên danh mục" style="min-width: 12rem">
                    <template #body="{ data }">
                        <span>{{ data.name }}</span>
                    </template>
                </Column>
                <Column field="slug" header="Slug" style="min-width: 12rem">
                    <template #body="{ data }">
                        <span>{{ data.slug }}</span>
                    </template>
                </Column>
                <Column field="parent_id" header="Danh mục cha" style="min-width: 12rem" sortable>
                    <template #body="{ data }">
                        <Tag :severity="data.parent ? 'primary' : 'danger'">
                            {{ data.parent ? data.parent.name : 'Không có' }}</Tag>
                    </template>
                </Column>
                <Column field="is_show" header="Hiển thị" style="min-width: 8rem" sortable>
                    <template #body="{ data }">
                        <Tag :severity="data.is_show ? 'success' : 'danger'">
                            {{ data.is_show ? 'Có' : 'Không' }}</Tag>
                    </template>
                </Column>
                <Column field="created_at" header="Ngày tạo" style="min-width: 12rem" sortable>
                    <template #body="{ data }">
                        <span>{{ formatDate(data.created_at) }}</span>
                    </template>
                </Column>
                <Column :exportable="false" header="Thao tác">
                    <template #body="{ data }">
                        <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="openModalEdit(data)" />
                        <Button icon="pi pi-trash" outlined rounded severity="danger" @click="deleteCategory(data)" />
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
        <Dialog v-model:visible="modal.create" modal header="Thêm danh mục tour" :style="{ width: '30rem' }">
            <form action="" @submit.prevent="create">
                <div>
                    <label for="slug" class="block text-sm mb-2 font-medium text-surface-700 dark:text-surface-200">Danh
                        mục cha</label>
                    <Select v-model="formCreate.parent_id" filter :options="categories" option-label="name"
                        placeholder="Chọn danh mục cha" emptyMessage="Chưa có dữ liệu" checkmark option-value="id" />
                </div>
                <div class="mt-4">
                    <label for="name" class="block text-sm mb-2 font-medium text-surface-700 dark:text-surface-200">Tên
                        danh mục</label>
                    <InputText id="name" type="text" class="mt-1 block w-full" placeholder="Nhập tên danh mục"
                        v-model="formCreate.name" @input="formCreate.slug = generateSlug(formCreate.name)" required
                        autocomplete="name" />
                </div>
                <div class="mt-4">
                    <label for="slug"
                        class="block text-sm mb-2 font-medium text-surface-700 dark:text-surface-200">Slug</label>
                    <InputText id="slug" type="text" class="mt-1 block w-full" v-model="formCreate.slug" required
                        autocomplete="slug" placeholder="Nhập slug"
                        @input="formCreate.slug = generateSlug(formCreate.slug)" />
                </div>
                <div class="mt-4">
                    <label class="flex items-center cursor-pointer ">
                        <Checkbox v-model="formCreate.is_show" binary class="mr-2" />
                        <span>Hiển thị </span>
                    </label>
                </div>
                <div class="flex justify-end mt-5 gap-2">
                    <Button type="button" label="Hủy" severity="secondary" @click="modal.create = false"></Button>
                    <Button type="submit" label="Lưu"></Button>
                </div>
            </form>

        </Dialog>
        <!-- modal edit -->
        <Dialog v-model:visible="modal.edit" modal header="Cập nhật danh mục" :style="{ width: '30rem' }">
            <form action="" @submit.prevent="update">
                <div>
                    <label for="slug" class="block text-sm mb-2 font-medium text-surface-700 dark:text-surface-200">Danh
                        mục cha</label>
                    <Select v-model="formEdit.parent_id" filter :options="categories" option-label="name"
                        placeholder="Chọn danh mục cha" emptyMessage="Chưa có dữ liệu" checkmark option-value="id" />
                </div>
                <div class="mt-4">
                    <label for="title"
                        class="block text-sm mb-2 font-medium text-surface-700 dark:text-surface-200">Tiêu
                        đề</label>
                    <InputText id="title" type="text" class="mt-1 block w-full" placeholder="Nhập tiêu đề"
                        v-model="formEdit.name" @input="formEdit.slug = generateSlug(formEdit.name)" required
                        autocomplete="title" />
                </div>
                <div class="mt-4">
                    <label for="slug"
                        class="block text-sm mb-2 font-medium text-surface-700 dark:text-surface-200">Slug</label>
                    <InputText id="slug" type="text" class="mt-1 block w-full" v-model="formEdit.slug" required
                        autocomplete="slug" placeholder="Nhập slug"
                        @input="formEdit.slug = generateSlug(formEdit.slug)" />
                </div>
                <div class="mt-4">
                    <label class="flex items-center cursor-pointer ">
                        <Checkbox v-model="formEdit.is_show" binary class="mr-2" />
                        <span>Hiển thị </span>
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