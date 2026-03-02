<script setup lang="ts">
import axios from 'axios';
import { Head, router, useForm } from '@inertiajs/vue3';
import useNotify from '@/Composables/useNotify.js';
import { useConfirm } from "primevue/useconfirm";
import AppLayout from '@/Layouts/AppLayout.vue';
import formatDate from '@/utils/formatDate';
import { debounce } from 'lodash';
import { ref, watch } from 'vue';
import { generateSlug } from '@/utils/slugify.js';
import TagsInput from '@/Components/admin/news/TagsInput.vue';

// state
const props = defineProps({
    data: Object,
    filters: Object,
    categories: Object
})
const notify = useNotify()
const confirm = useConfirm();
const loading = ref(false)
const menu = ref(null);
const selectedRow = ref(null);
const search = ref(props.filters.search || '');

const modal = ref({
    create: false,
    edit: false,
    editContent: false
});

const formCreate = useForm({
    title: '',
    slug: '',
    tags: [],
    category_id: null,
    short_description: '',
    thumbnail: null,
    is_show: true,
    is_pin: true,
});

const formEdit = useForm({
    id: null,
    title: '',
    slug: '',
    tags: [],
    category_id: null,
    short_description: '',
    thumbnail: '',
    is_show: true,
    is_pin: true,
});

const formContent = useForm({
    id: null,
    content: '',
});

const items = [
    { label: 'Sửa bài viết', icon: 'pi pi-pencil', command: () => openModalEdit(selectedRow.value) },
    { label: 'Sửa nội dung', icon: 'pi pi-pencil', command: () => openModalEditContent(selectedRow.value) },
    { label: 'Xoá bài viết', icon: 'pi pi-trash', command: () => deleteNews(selectedRow.value) }
];

const openMenu = (event, rowData) => {
    selectedRow.value = rowData;
    menu.value.toggle(event);
};
const lazyParams = ref({
    first: (props.data.current_page - 1) * props.data.per_page,
    rows: props.data.per_page,
    page: props.data.current_page - 1,
    sortField: props.filters.sortField || 'created_at',
    sortOrder: props.filters.sortOrder ?? -1,
});

const fetchData = debounce((page = 1) => {
    loading.value = true;
    router.get(route('admin.news'), {
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
    fetchData(1);
});

function onPage(event) {
    lazyParams.value = { ...lazyParams.value, ...event };
    fetchData(event.page + 1);
}

function onSort(event) {
    lazyParams.value.sortField = event.sortField;
    lazyParams.value.sortOrder = event.sortOrder;
    fetchData(lazyParams.value.page + 1);
}

// show edit modal
const openModalEdit = ({ tags, is_pin, is_show, ...rest }) => {
    Object.assign(formEdit, {
        ...rest,
        thumbnail: null,
        tags: tags.map(t => t.name),
        is_pin: !!is_pin,
        is_show: !!is_show
    })
    modal.value.edit = true
}


const openModalEditContent = async (data) => {
    try {
        const res = await axios.get(route('admin.news.content', data.id))
        formContent.content = res.data.content
        formContent.id = data.id
        modal.value.editContent = true
    } catch (err) {
        console.error(err)
    }
}
const onFileChange = (e: Event, formType: 'create' | 'edit') => {
    const target = e.target as HTMLInputElement
    if (target.files && target.files[0]) {
        const file = target.files[0] as any
        if (formType === 'create') {
            formCreate.thumbnail = file
        } else if (formType === 'edit') {
            formEdit.thumbnail = file
        }
    }
}

// call api
const create = () => {
    formCreate.post(route('admin.news.store'), {
        onSuccess: () => {
            formCreate.reset();
            modal.value.create = false;
            notify.success('Thêm bài viết thành công');
        },
        onError: (errors) => {
            Object.values(errors).forEach(errorArray => {
                notify.error(errorArray)
            });
        }
    })
}
const update = () => {
    formEdit.post(route('admin.news.update', formEdit.id), {
        onSuccess: () => {
            notify.success('Cập nhật bài viết thành công');
            modal.value.edit = false;
        },
        onError: (errors) => {
            Object.values(errors).forEach(errorArray => {
                notify.error(errorArray)
            });
        }
    })
}
const updateContent = () => {
    formContent.post(route('admin.news.content.update', formContent.id), {
        onSuccess: () => {
            notify.success('Cập nhật nội dung bài viết thành công');
            modal.value.editContent = false;
        },
        onError: (errors) => {
            Object.values(errors).forEach(errorArray => {
                notify.error(errorArray)
            });
        }
    })
}

const deleteNews = (data) => {
    confirm.require({
        message: 'Bạn có chắc muốn xóa bài viết này?',
        header: 'Xác nhận xoá',
        acceptLabel: 'Xoá',
        rejectLabel: 'Huỷ',
        acceptClass: 'p-button-danger',
        accept: () => {
            router.delete(route('admin.news.destroy', data.id), {
                onSuccess: () => {
                    notify.success('Xóa bài viết thành công')
                    fetchData(lazyParams.value.page + 1)
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

    <Head title="Quản lý bài viết" />
    <AppLayout>
        <div class="card">
            <DataTable :value="data.data" dataKey="id" stripedRows responsiveLayout="scroll" :paginator="true"
                :rows="lazyParams.rows" :first="lazyParams.first" :totalRecords="data.total" :loading="loading"
                :rowsPerPageOptions="[5, 10, 25, 50]" :lazy="true" @page="onPage" @sort="onSort">
                <template #header>
                    <div class="flex flex-wrap gap-2 mb-5 items-center justify-between">
                        <h2 class="m-0 text-2xl font-sans">Quản lý bài viết</h2>
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
                <Column field="thumbnail" header="Hình ảnh">
                    <template #body="{ data }">
                        <Image preview :src="data.thumbnail" alt="" class="w-16 h-16 object-cover" />
                    </template>
                </Column>
                <Column field="title" header="Tiêu đề">
                    <template #body="{ data }">
                        <span class="mr-2">{{ data.title }}</span>
                        <Tag v-if="data.is_pin" severity="primary">ghim</Tag>
                    </template>
                </Column>
                <Column field="category.name" header="Danh mục">
                    <template #body="{ data }">
                        <Tag severity="primary">{{ data.category?.name }}</Tag>
                    </template>
                </Column>
                <Column field="author_id" header="Tác giả">
                    <template #body="{ data }">
                        <Tag severity="info">{{ data.author?.account }}</Tag>
                    </template>
                </Column>
                <Column field="views" header="Lượt xem" sortable>
                    <template #body="{ data }">
                        <Tag severity="info">{{ data.views }}</Tag>
                    </template>
                </Column>
                <Column field="is_show" header="Trạng thái">
                    <template #body="{ data }">
                        <Tag :severity="data.is_show ? 'primary' : 'danger'">
                            {{ data.is_show ? 'Hiển thị' : 'Ẩn' }}</Tag>
                    </template>
                </Column>
                <Column field="created_at" header="Ngày tạo" sortable>
                    <template #body="{ data }">
                        <span>{{ formatDate(data.created_at) }}</span>
                    </template>
                </Column>
                <Column :exportable="false" header="Thao tác">
                    <template #body="{ data }">
                        <div>
                            <Button icon="pi pi-ellipsis-v" class="p-button-text p-button-plain p-button-rounded"
                                @click="openMenu($event, data)" />
                            <Menu ref="menu" popup :model="items" class="!min-w-40" />
                        </div>
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
        <!-- modal create news-->
        <Dialog v-model:visible="modal.create" modal header="Thêm bài viết" :style="{ width: '35rem' }">
            <form action="" @submit.prevent="create">
                <div class="mb-4">
                    <label for="title"
                        class="block text-sm mb-2 font-medium text-surface-700 dark:text-surface-200">Danh
                        mục</label>
                    <Select v-model="formCreate.category_id" filter checkmark optionValue="id"
                        :options="categories as any" optionLabel="name" placeholder="Chọn danh mục"
                        class="w-full md:w-56" />
                </div>
                <div class="mb-4">
                    <label for="title"
                        class="block text-sm mb-2 font-medium text-surface-700 dark:text-surface-200">Tiêu
                        đề bài viết</label>
                    <InputText id="title" type="text" class="mt-1 block w-full" placeholder="Nhập tiêu đề bài viết"
                        v-model="formCreate.title" @input="formCreate.slug = generateSlug(formCreate.title)"
                        autocomplete="title" />
                </div>
                <div class="mb-4">
                    <label for="title" class="block text-sm mb-2 font-medium text-surface-700 dark:text-surface-200">Thẻ
                        tags <i>(nhấn dấu "," hoặc Enter để thêm)</i></label>
                    <TagsInput v-model="formCreate.tags" />
                </div>
                <div class="mb-4">
                    <label for="thumbnail"
                        class="block text-sm mb-2 font-medium text-surface-700 dark:text-surface-200">Hình
                        ảnh đại diện</label>
                    <InputText type="file" class="mt-1 block w-full" size="small"
                        @change="e => onFileChange(e, 'create')" />
                </div>
                <div class="mb-4">
                    <label for="title" class="block text-sm mb-2 font-medium text-surface-700 dark:text-surface-200">Mô
                        tả ngắn</label>
                    <Textarea id="address" rows="6" class="w-full" v-model="formCreate.short_description"
                        autocomplete="description" />
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
        <!-- modal edit news -->
        <Dialog v-model:visible="modal.edit" modal header="Cập nhật bài viết" :style="{ width: '35rem' }">
            <form action="" @submit.prevent="update">
                <div class="mb-4">
                    <label for="title"
                        class="block text-sm mb-2 font-medium text-surface-700 dark:text-surface-200">Danh
                        mục</label>
                    <Select v-model="formEdit.category_id" filter checkmark optionValue="id"
                        :options="categories as any" optionLabel="name" placeholder="Chọn danh mục"
                        class="w-full md:w-56" />
                </div>
                <div class="mb-4">
                    <label for="title"
                        class="block text-sm mb-2 font-medium text-surface-700 dark:text-surface-200">Tiêu
                        đề bài viết</label>
                    <InputText id="title" type="text" class="mt-1 block w-full" placeholder="Nhập tiêu đề bài viết"
                        v-model="formEdit.title" @input="formEdit.slug = generateSlug(formEdit.title)"
                        autocomplete="title" />
                </div>
                <div class="mb-4">
                    <label for="title" class="block text-sm mb-2 font-medium text-surface-700 dark:text-surface-200">Thẻ
                        tags <i>(nhấn dấu "," hoặc Enter để thêm)</i></label>
                    <TagsInput v-model="formEdit.tags" />
                </div>
                <div class="mb-4">
                    <label for="thumbnail"
                        class="block text-sm mb-2 font-medium text-surface-700 dark:text-surface-200">Hình
                        ảnh đại diện</label>
                    <InputText type="file" class="mt-1 block w-full" size="small"
                        @change="e => onFileChange(e, 'edit')" />

                </div>
                <div class="mb-4">
                    <label for="title" class="block text-sm mb-2 font-medium text-surface-700 dark:text-surface-200">Mô
                        tả ngắn</label>
                    <Textarea id="address" rows="6" class="w-full" v-model="formEdit.short_description"
                        autocomplete="description" />
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
        <!-- modal content -->
        <Dialog v-model:visible="modal.editContent" modal header="Cập nhật nội dung bài viết"
            :style="{ width: '50rem' }">
            <form action="" @submit.prevent="updateContent">
                <div class="mb-4">
                    <Editor v-model="formContent.content" editorStyle="height: 500px" />
                </div>
                <div class="flex justify-end mt-5 gap-2">
                    <Button type="button" label="Hủy" severity="secondary" @click="modal.editContent = false"></Button>
                    <Button type="submit" label="Lưu"></Button>
                </div>
            </form>
        </Dialog>
    </AppLayout>
</template>
<style scoped>
.p-select {
    width: 100%;
}

.p-paginator-content {
    flex-wrap: nowrap !important;
}
</style>
