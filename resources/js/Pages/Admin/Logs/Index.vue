<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import useNotify from '@/Composables/useNotify.js';
import { useConfirm } from "primevue/useconfirm";
import AppLayout from '@/Layouts/AppLayout.vue';
import formatDate from '@/utils/formatDate';
import { debounce } from 'lodash';
import { ref, watch } from 'vue';

// state
const props = defineProps({
    data: Object,
    filters: Object
})

const notify = useNotify()
const confirm = useConfirm();
const loading = ref(false)

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
    router.get(route('admin.logs'), {
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


// call api
const deleteLog = (data) => {
    confirm.require({
        message: 'Bạn có chắc muốn xóa lịch sử này?',
        header: 'Xác nhận xoá',
        acceptLabel: 'Xoá',
        rejectLabel: 'Huỷ',
        acceptClass: 'p-button-danger',
        accept: () => {
            router.delete(route('admin.logs.destroy', data.id), {
                onSuccess: () => {
                    notify.success('Xóa lịch sử thành công')
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

    <Head title="Quản lý nhật ký hoạt động" />
    <AppLayout>
        <div class="card">
            <DataTable :value="data.data" dataKey="id" stripedRows responsiveLayout="scroll" :paginator="true"
                :rows="lazyParams.rows" :first="lazyParams.first" :totalRecords="data.total" :loading="loading"
                :rowsPerPageOptions="[5, 10, 25, 50]" :lazy="true" @page="onPage" @sort="onSort">
                <template #header>
                    <div class="flex flex-wrap gap-2 mb-5 items-center justify-between">
                        <h2 class="m-0 text-2xl font-sans">Quản lý nhật ký hoạt động</h2>
                        <InputText v-model="search" placeholder="Tìm kiếm..." />
                    </div>
                </template>
                <Column field="stt" header="STT">
                    <template #body="{ index }">
                        <span>{{ index + 1 + lazyParams.first }}</span>
                    </template>
                </Column>
                <Column field="name" header="Tài khoản" style="min-width: 12rem">
                    <template #body="{ data }">
                        <Tag severity="primary">{{ data.user.account }}</Tag>
                    </template>
                </Column>

                <Column field="ip_address" header="Địa chỉ IP" style="min-width: 12rem">
                    <template #body="{ data }">
                        <span>{{ data.ip_address }}</span>
                    </template>
                </Column>
                <Column field="content" header="Nội dung" style="min-width: 12rem">
                    <template #body="{ data }">
                        <span v-html="data.content"></span>
                    </template>
                </Column>
                <Column field="created_at" header="Ngày tạo" style="min-width: 12rem">
                    <template #body="{ data }">
                        <span>{{ formatDate(data.created_at) }}</span>
                    </template>
                </Column>
                <Column :exportable="false" header="Thao tác">
                    <template #body="{ data }">
                        <Button icon="pi pi-trash" outlined rounded severity="danger" @click="deleteLog(data)" />
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
<style>
.p-select {
    width: 100%;
}

.p-paginator-content {
    flex-wrap: nowrap !important;
}
</style>