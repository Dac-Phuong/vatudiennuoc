<script setup lang="ts">
import FileUploads from '@/Components/admin/FileUploads.vue';
import useNotify from '@/Composables/useNotify';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import { ref } from 'vue';

const props = defineProps({
    settings: Object,
})
const notify = useNotify()
const submitting = ref(false);
const images = ref(JSON.parse(props.settings?.banners) || null);
const formBase = useForm({
    name: '',
    image_seo: null,
    banners: null
})

const formContact = useForm({
    phone: '',
    email: '',
    address: '',
    map: '',
    link_zalo: '',
    link_facebook: ''
})
onMounted(() => {

    Object.assign(formBase, props.settings);
    Object.assign(formContact, props.settings);
});


const submitBase = () => {
    submitting.value = true;
    formBase.post(route('admin.settings.update'), {
        onSuccess: () => {
            submitting.value = false;
            notify.success('Lưu thành công');
        },
        onError: () => {
            notify.success('Lưu thất bạy');
            submitting.value = false;
        },
        onFinish: () => {
            submitting.value = false;

        }
    })
}

const submitContact = () => {
    submitting.value = true;
    formContact.post(route('admin.settings.update'), {
        onSuccess: () => {
            submitting.value = false;
            notify.success('Lưu thành công');
        },
        onError: () => {
            notify.success('Lưu thất bạy');
            submitting.value = false;
        },
        onFinish: () => {
            submitting.value = false;

        }
    })
}

const handleUploaded = (files) => {
    formBase.banners = files ?? [];
    notify.success('Tải ảnh lên thành công!');
}
console.log(images.value);

</script>

<template>

    <Head title="Quản lý cài đặt" />
    <AppLayout>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Cài đặt</h3>
            </div>
            <div class="card-body">
                <Tabs value="0">
                    <TabList>
                        <Tab value="0">Cơ bản</Tab>
                        <Tab value="1">Liên hệ</Tab>
                    </TabList>
                    <TabPanels>
                        <TabPanel value="0">
                            <div class="max-w-3xl">
                                <form @submit.prevent="submitBase">
                                    <div class="mb-4">
                                        <label for="name"
                                            class="block text-sm mb-2 font-medium text-surface-700 dark:text-surface-200">Tên
                                            trang</label>
                                        <InputText v-model="formBase.name" id="name" type="text"
                                            class="mt-1 block w-full" placeholder="Nhập tên trang"
                                            autocomplete="name" />
                                    </div>

                                    <div class="mb-4">
                                        <label for="seo"
                                            class="block text-sm mb-2 font-medium text-surface-700 dark:text-surface-200">Ảnh
                                            SEO</label>
                                        <InputText v-model="formBase.image_seo" id="seo" type="file"
                                            class="mt-1 block w-full" placeholder="Chọn ảnh SEO" autocomplete="seo" />
                                    </div>
                                    <div class="mb-4">
                                        <label for="seo"
                                            class="block text-sm mb-2 font-medium text-surface-700 dark:text-surface-200">Ảnh
                                            Banner</label>
                                        <FileUploads name="images[]" url="/api/admin/products/upload" :multiple="false"
                                            :images="images" accept="image/*" @uploaded="handleUploaded" />
                                    </div>
                                    <Button type="submit" label="Lưu ngay" :loading="submitting"
                                        class="btn btn-primary" />
                                </form>
                            </div>
                        </TabPanel>
                        <TabPanel value="1">
                            <div class="max-w-3xl">
                                <form @submit.prevent="submitContact">
                                    <div class="mb-4">
                                        <label for="phone"
                                            class="block text-sm mb-2 font-medium text-surface-700 dark:text-surface-200">
                                            Số điện thoại</label>
                                        <InputText v-model="formContact.phone" id="phone" type="number"
                                            class="mt-1 block w-full" placeholder="Nhập số điện thoại"
                                            autocomplete="phone" />
                                    </div>
                                    <div class="mb-4">
                                        <label for="email"
                                            class="block text-sm mb-2 font-medium text-surface-700 dark:text-surface-200">
                                            Địa chỉ Email</label>
                                        <InputText v-model="formContact.email" id="email" type="email"
                                            class="mt-1 block w-full" placeholder="Nhập địa chỉ Email"
                                            autocomplete="email" />
                                    </div>
                                    <div class="mb-4">
                                        <label for="address"
                                            class="block text-sm mb-2 font-medium text-surface-700 dark:text-surface-200">
                                            Địa chỉ</label>
                                        <InputText v-model="formContact.address" id="address" type="text"
                                            class="mt-1 block w-full" placeholder="Nhập địa chỉ"
                                            autocomplete="address" />
                                    </div>
                                    <div class="mb-4">
                                        <label for="zalo"
                                            class="block text-sm mb-2 font-medium text-surface-700 dark:text-surface-200">
                                            Zalo</label>
                                        <InputText v-model="formContact.link_zalo" id="zalo" type="text"
                                            class="mt-1 block w-full" placeholder="Nhập link zalo"
                                            autocomplete="zalo" />
                                    </div>
                                    <div class="mb-4">
                                        <label for="link_facebook"
                                            class="block text-sm mb-2 font-medium text-surface-700 dark:text-surface-200">
                                            Facebook</label>
                                        <InputText v-model="formContact.link_facebook" id="link_facebook" type="text"
                                            class="mt-1 block w-full" placeholder="Nhập link facebook"
                                            autocomplete="link_facebook" />
                                    </div>

                                    <Button type="submit" class="btn btn-primary">Lưu ngay</Button>
                                </form>
                            </div>
                        </TabPanel>

                    </TabPanels>
                </Tabs>
            </div>
        </div>
    </AppLayout>
</template>
