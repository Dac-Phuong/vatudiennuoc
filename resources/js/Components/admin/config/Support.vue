<template>
    <form class="space-y-4" @submit.prevent="submitForm">
        <div class="space-y-5">
            <div v-for="item in socials" :key="item.key"
                class="flex md:flex-row justify-center gap-4 shadow-xl items-center md:items-center p-4 border dark:border-gray-800 rounded-xl transition">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center " :class="item.color">
                    <i :class="`pi ${item.icon}`" class="title"></i>
                </div>

                <div class="flex-1 space-y-1">
                    <label class="font-semibold label text-gray-700 dark:text-gray-300">
                        {{ item.label }}
                    </label>

                    <InputText v-model="form[item.key]" class="w-full" :placeholder="item.placeholder" />

                    <p class="lable-sm text-gray-500 dark:text-gray-300">
                        {{ 'Example' }}: {{ item.example }}
                    </p>
                </div>

                <!-- PREVIEW -->
                <div v-if="form[item.key] && item.openlink" class="pt-2 md:pt-0">
                    <a :href="form[item.key]" target="_blank" class="text-sm text-primary font-medium hover:underline">
                        {{ 'Open link' }}
                    </a>
                </div>
            </div>
            <!-- ACTION -->
            <div class="pt-4 text-right">
                <Button label="Lưu cấu hình" :loading="loading" type="submit" icon="pi pi-save" />
            </div>
        </div>
    </form>
</template>
<script setup>
import useNotify from '@/Composables/useNotify';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
const notify = useNotify();

const props = defineProps({
    locale: String,
    translations: Object,
    settings: Object,
})
const loading = ref(false);

const form = useForm({
    phone: props.settings?.phone ?? '',
    email: props.settings?.email ?? '',
    zalo: props.settings?.zalo ?? '',
    facebook: props.settings?.facebook ?? '',
    telegram: props.settings?.telegram ?? '',
    address: props.settings?.address ?? ''

})

const socials = [
    {
        key: 'phone',
        label: 'Số điện thoại',
        icon: 'pi pi-phone',
        color: 'bg-green-50 text-green-600',
        placeholder: '+84999999999',
        example: '+84999999999',
        openlink: false

    },

    {
        key: 'email',
        label: 'Email',
        icon: 'pi pi-envelope',
        color: 'bg-red-50 text-red-600',
        placeholder: 'support@gmail.com',
        example: 'support@gmail.com',
        openlink: false
    },
    {
        key: 'zalo',
        label: 'Hỗ trợ Zalo',
        icon: 'pi pi-comments',
        color: 'bg-blue-50 text-blue-600',
        placeholder: 'https://zalo.me/0999999999',
        example: 'https://zalo.me/0999999999',
        openlink: true

    },
    {
        key: 'facebook',
        label: 'Link Facebook',
        icon: 'pi pi-facebook',
        color: 'bg-indigo-50 text-indigo-600',
        placeholder: 'https://facebook.com/tenfanpage',
        example: 'https://facebook.com/tenfanpage',
        openlink: true
    },
    {
        key: 'telegram',
        label: 'Link Telegram',
        icon: 'pi pi-send',
        color: 'bg-sky-50 text-sky-600',
        placeholder: 'https://t.me/username',
        example: 'https://t.me/username',
        openlink: true
    },
    {
        key: 'address',
        label: 'Địa chỉ cửa hàng',
        icon: 'pi pi-map-marker',
        color: 'bg-orange-50 text-orange-600',
        placeholder: '123 Đường ABC, Quận XYZ, TP. HCM',
        example: '123 Đường ABC, Quận XYZ, TP. HCM',
        openlink: false
    },
]

const submitForm = () => {
    loading.value = true
    form.post(route('admin.settings.update'), {
        onError: (errors) => {
            Object.values(errors).flat().forEach(msg => {
                notify.error(msg)
            })
            loading.value = false
        },
        onSuccess: () => {
            notify.success('Đã lưu cấu hình thành công')
            loading.value = false
        },
        onFinish: () => {
            loading.value = false
        },
    })

}
</script>