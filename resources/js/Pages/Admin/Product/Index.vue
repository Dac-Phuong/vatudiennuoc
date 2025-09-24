<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import useNotify from '@/Composables/useNotify.js';
import { useConfirm } from "primevue/useconfirm";
import AppLayout from '@/Layouts/AppLayout.vue';
import formatDate from '@/utils/formatDate';
import { debounce } from 'lodash';
import { ref, watch } from 'vue';
import formatNumber from '@/utils/formatNumber';

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
const search = ref(props.filters.search || '');

const lazyParams = ref({
    first: (props.data.current_page - 1) * props.data.per_page,
    rows: props.data.per_page,
    page: props.data.current_page - 1,
    sortField: props.filters.sortField || 'created_at',
    sortOrder: props.filters.sortOrder ?? -1,
});

const fetchData = debounce((page = 1) => {
    loading.value = true;
    router.get(route('admin.products'), {
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

const submitDelete = (data) => {
    confirm.require({
        message: 'Bạn có chắc muốn xóa bài viết này?',
        header: 'Xác nhận xoá',
        acceptLabel: 'Xoá',
        rejectLabel: 'Huỷ',
        acceptClass: 'p-button-danger',
        accept: () => {
            router.delete(route('admin.products.destroy', data.id), {
                onSuccess: () => {
                    notify.success('Xóa sản phẩm thành công')
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

    <Head title="Quản lý sản phẩm" />
    <AppLayout>
        <div class="card">
            <DataTable :value="data.data" dataKey="id" stripedRows responsiveLayout="scroll" :paginator="true"
                :rows="lazyParams.rows" :first="lazyParams.first" :totalRecords="data.total" :loading="loading"
                :rowsPerPageOptions="[5, 10, 25, 50]" :lazy="true" @page="onPage" @sort="onSort">
                <template #header>
                    <div class="flex flex-wrap gap-2 mb-5 items-center justify-between">
                        <h2 class="m-0 text-2xl font-sans">Quản lý sản phẩm</h2>
                        <div class="flex gap-2 justify-center">
                            <InputText v-model="search" placeholder="Tìm kiếm..." />
                            <Link :href="route('admin.products.create')"
                                class="p-button p-component bg-primary px-4 py-3 rounded-md text-white flex items-center gap-2">
                            <span class="p-button-icon pi pi-plus"></span>
                            <span class="p-button-label">Thêm mới</span>
                            </Link>
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
                <Column field="name" header="Tên sản phẩm" style="max-width: 200px;">
                    <template #body="{ data }">
                        <span>{{ data.name }}</span>
                    </template>
                </Column>
                <Column field="category.name" header="Danh mục">
                    <template #body="{ data }">
                        <Tag severity="primary">{{ data.category?.name || 'Không xác định' }}</Tag>
                    </template>
                </Column>

                <Column field="quantity" header="Số lượng">
                    <template #body="{ data }">
                        <Tag severity="info">{{ formatNumber(data.quantity) }}</Tag>
                    </template>
                </Column>
                <Column field="price" header="giá tiền" sortable>
                    <template #body="{ data }">
                        <Tag severity="info">{{ formatNumber(data.price) }} vnđ</Tag>
                    </template>
                </Column>
                <Column field="discount" header="Khuyến mãi" sortable>
                    <template #body="{ data }">
                        <Tag severity="info">{{ data.discount }} %</Tag>
                    </template>
                </Column>
                <Column field="status" header="Trạng thái">
                    <template #body="{ data }">
                        <Tag :severity="data.status ? 'primary' : 'danger'">
                            {{ data.status ? 'Hiển thị' : 'Ẩn' }}</Tag>
                    </template>
                </Column>
                <Column field="created_at" header="Ngày tạo" sortable>
                    <template #body="{ data }">
                        <span>{{ formatDate(data.created_at) || 'Không xác định' }}</span>
                    </template>
                </Column>
                <Column :exportable="false" header="Thao tác">
                    <template #body="{ data }">
                        <Link :href="route('admin.products.edit', data.id)">
                        <Button icon="pi pi-pencil" outlined rounded class="mr-2" />
                        </Link>
                        <Button icon="pi pi-trash" outlined rounded severity="danger" @click="submitDelete(data)" />
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
