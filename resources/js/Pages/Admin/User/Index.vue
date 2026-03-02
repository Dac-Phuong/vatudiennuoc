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
    users: Object,
    filters: Object
})

const notify = useNotify()
const confirm = useConfirm();
const loading = ref(false)
const roles = ref([
    { name: 'Quản lý', role: 100 },
    { name: 'Nhân viên', role: 1 },
    { name: 'Người dùng', role: 0 },
]);
const locked = ref([
    { name: 'Có ', locked: 1 },
    { name: 'Không ', locked: 0 },
]);
const modal = ref({
    edit: false,
});

const formEditUser = useForm({
    id: null,
    account: '',
    email: '',
    role: 0,
    locked: false,
    phone: '',
    password: '',
});


const search = ref(props.filters.search || '');
const lazyParams = ref({
    first: (props.users.current_page - 1) * props.users.per_page,
    rows: props.users.per_page,
    page: props.users.current_page - 1,
    sortField: props.filters.sortField || 'created_at',
    sortOrder: props.filters.sortOrder ?? -1,
});

const fetchUsers = debounce((page = 1) => {
    loading.value = true;
    router.get(route('admin.users'), {
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
    fetchUsers(1);
});

function onPage(event) {
    lazyParams.value = { ...lazyParams.value, ...event };
    fetchUsers(event.page + 1);
}

function onSort(event) {
    lazyParams.value.sortField = event.sortField;
    lazyParams.value.sortOrder = event.sortOrder;
    fetchUsers(lazyParams.value.page + 1);
}

// get date
const editUser = (user) => {
    Object.assign(formEditUser, user)
    modal.value.edit = true
}

// call api
const submitEditUser = () => {
    formEditUser.post(route('admin.users.update', formEditUser.id), {
        onSuccess: () => {
            notify.success('Cập nhật người dùng thành công')
            modal.value.edit = false;
            formEditUser.reset();
        },
        onError: (errors) => {
            Object.values(errors).forEach(errorArray => {
                notify.error(errorArray)
            });
        }
    })
}

const deleteUser = (user) => {
    confirm.require({
        message: 'Bạn có chắc muốn xóa người dùng này?',
        header: 'Xác nhận xoá',
        acceptLabel: 'Xoá',
        rejectLabel: 'Huỷ',
        acceptClass: 'p-button-danger',
        accept: () => {
            router.delete(route('admin.users.destroy', user.id), {
                onSuccess: () => {
                    notify.success('Xóa người dùng thành công')
                    fetchUsers(lazyParams.value.page + 1)
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

const getRole = (role) => ({
    100: { label: "Quản lý", color: "danger" },
    1: { label: "Nhân viên", color: "warn" },
    0: { label: "Người dùng", color: "primary" }
}[role]);

</script>

<template>

    <Head title="Quản lý người dùng" />
    <AppLayout>
        <div class="card">
            <DataTable :value="users.data" dataKey="id" stripedRows responsiveLayout="scroll" :paginator="true"
                :rows="lazyParams.rows" :first="lazyParams.first" :totalRecords="users.total" :loading="loading"
                :rowsPerPageOptions="[5, 10, 25, 50]" :lazy="true" @page="onPage" @sort="onSort">
                <template #header>
                    <div class="flex flex-wrap gap-2 mb-5 items-center justify-between">
                        <h2 class="m-0 text-2xl">Quản lý người dùng</h2>
                        <InputText v-model="search" placeholder="Tìm kiếm..." />

                    </div>
                </template>
                <Column field="stt" header="STT">
                    <template #body="{ index }">
                        <span>{{ index + 1 + lazyParams.first }}</span>
                    </template>
                </Column>
                <Column field="account" header="Họ và tên">
                    <template #body="{ data }">
                        <span>{{ data.account }}</span>
                    </template>
                </Column>
                <Column field="email" header="Email" style="min-width: 16rem">
                    <template #body="{ data }">
                        <span>{{ data.email }}</span>
                    </template>
                </Column>
                <Column field="phone" header="Số điện thoại" style="min-width: 16rem">
                    <template #body="{ data }">
                        <span>{{ data.phone }}</span>
                    </template>
                </Column>
                <Column field="role" header="Vai trò" sortable>
                    <template #body="{ data }">
                        <Tag :severity="getRole(data.role).color">
                            {{ getRole(data.role).label }}
                        </Tag>
                    </template>
                </Column>
                <Column field="locked" header="Khóa tài khoản">
                    <template #body="{ data }">
                        <Tag :severity="data.locked ? 'danger' : 'primary'">{{
                            data.locked ? 'Có' : 'Không' }}</Tag>
                    </template>
                </Column>
                <Column field="created_at" header="Ngày tạo" sortable>
                    <template #body="{ data }">
                        <span>{{ formatDate(data.created_at) }}</span>
                    </template>
                </Column>
                <Column :exportable="false" header="Thao tác">
                    <template #body="{ data }">
                        <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="editUser(data)" />
                        <Button icon="pi pi-trash" outlined rounded severity="danger" @click="deleteUser(data)" />
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
        <!-- modal edit -->
        <Dialog v-model:visible="modal.edit" modal header="Cập nhật người dùng" :style="{ width: '30rem' }">
            <form action="" @submit.prevent="submitEditUser">
                <div>
                    <label for="account"
                        class="block text-sm mb-2 font-medium text-surface-700 dark:text-surface-200">Họ và
                        tên</label>
                    <InputText id="account" type="text" class="mt-1 block w-full" placeholder="Họ và tên"
                        v-model="formEditUser.account" required autocomplete="account" />
                </div>
                <div class="mt-4">
                    <label for="email" class="block text-sm mb-2 font-medium text-surface-700 dark:text-surface-200">
                        Địa
                        chỉ
                        Email</label>
                    <InputText id="email" type="text" class="mt-1 block w-full" v-model="formEditUser.email" required
                        autocomplete="email" placeholder="Nhập địa chỉ email" />
                </div>
                <div class="mt-4">
                    <label for="phone" class="block text-sm mb-2 font-medium text-surface-700 dark:text-surface-200">Số
                        điện thoại</label>
                    <InputText id="phone" type="text" class="mt-1 block w-full" placeholder="Nhập số điện thoại"
                        v-model="formEditUser.phone" autocomplete="phone" />
                </div>
                <div class="mt-4">
                    <label for="password"
                        class="block text-sm mb-2 font-medium text-surface-700 dark:text-surface-200">Mật
                        khẩu</label>
                    <InputText id="password" placeholder="Nhập mật khẩu" type="text" class="mt-1 block w-full"
                        v-model="formEditUser.password" autocomplete="password" />
                </div>
                <div class="mt-4">
                    <label for="role" class="block text-sm mb-2 font-medium text-surface-700 dark:text-surface-200">Vai
                        trò</label>
                    <Select v-model="formEditUser.role" optionValue="role" :options="roles" optionLabel="name"
                        placeholder="Chọn vai trò" class="w-full md:w-56" />
                </div>
                <div class="mt-4">
                    <label for="locked"
                        class="block text-sm mb-2 font-medium text-surface-700 dark:text-surface-200">Khóa tài
                        khoản</label>
                    <Select v-model="formEditUser.locked" optionValue="locked" :options="locked" optionLabel="name"
                        placeholder="Chọn trạng thái" class="w-full md:w-56" />
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