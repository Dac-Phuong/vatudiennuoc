<script setup lang="ts">
import Support from '@/Components/admin/config/Support.vue';
import System from '@/Components/admin/config/System.vue';
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
const images = ref(props.settings?.banners ? JSON.parse(props.settings.banners) : null);
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
                        <Tab value="0">Hệ thống</Tab>
                        <Tab value="1">Hỗ trợ</Tab>
                    </TabList>
                    <TabPanels>
                        <TabPanel value="0">
                            <System :settings="settings" />
                        </TabPanel>
                        <TabPanel value="1">
                            <Support :settings="settings" />
                        </TabPanel>

                    </TabPanels>
                </Tabs>
            </div>
        </div>
    </AppLayout>
</template>
