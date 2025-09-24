<script setup>
import { ref, reactive } from 'vue'
import ClientLayout from '@/Layouts/ClientLayout.vue'
import { Head, usePage } from '@inertiajs/vue3'
import Breadcrumbs from '@/Components/client/Breadcrumbs.vue'

const { settings } = usePage().props;
// Form data
const form = reactive({
    name: '',
    email: '',
    phone: '',
    message: ''
})

// Form validation errors
const errors = reactive({
    name: '',
    email: '',
    phone: '',
    message: ''
})

// Loading state
const isSubmitting = ref(false)

// Validation function
const validateForm = () => {
    // Reset errors
    Object.keys(errors).forEach(key => errors[key] = '')

    let isValid = true

    if (!form.name.trim()) {
        errors.name = 'Vui lòng nhập họ và tên'
        isValid = false
    }

    if (!form.email.trim()) {
        errors.email = 'Vui lòng nhập email'
        isValid = false
    } else if (!/\S+@\S+\.\S+/.test(form.email)) {
        errors.email = 'Email không hợp lệ'
        isValid = false
    }

    if (!form.phone.trim()) {
        errors.phone = 'Vui lòng nhập số điện thoại'
        isValid = false
    }

    if (!form.message.trim()) {
        errors.message = 'Vui lòng nhập nội dung'
        isValid = false
    }

    return isValid
}

// Submit form
const submitForm = async () => {
    if (!validateForm()) return
    isSubmitting.value = true

}
</script>
<template>

    <Head title="Liên hệ" />
    <ClientLayout>
        <Breadcrumbs :items="[{ label: 'Liên hệ' }]" />
        <div class=" bg-gray-100 p-5">
            <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left Panel - Store Info & Contact Form -->
                <div class="space-y-6">
                    <!-- Store Information Card -->
                    <Card class="shadow-lg">
                        <template #header>
                            <div class="bg-primary text-white p-2 rounded-t-lg flex items-center justify-center">
                                <h2 class="text-xl font-bold text-white mb-0">Cửa hàng của chúng tôi</h2>
                            </div>
                        </template>
                        <template #content>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4">
                                <!-- Location -->
                                <div class="flex items-start space-x-3">
                                    <div class="bg-blue-100 p-4 flex justify-center items-center rounded-full">
                                        <i class="pi pi-map-marker text-primary"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-800 text-xl mb-0">Địa chỉ</h3>
                                        <p class="text-gray-600 text-sm">{{ settings.address || "Hà Nội" }}</p>
                                    </div>
                                </div>

                                <!-- Working Hours -->
                                <div class="flex items-start space-x-3">
                                    <div class="bg-blue-100 p-4 flex justify-center items-center rounded-full">
                                        <i class="pi pi-clock text-primary"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-800 text-xl mb-0">Thời gian làm việc</h3>
                                        <p class="text-gray-600 text-sm mb-0">8h - 22h</p>
                                        <p class="text-gray-600 text-sm">Từ thứ 2 đến thứ 7</p>
                                    </div>
                                </div>

                                <!-- Hotline -->
                                <div class="flex items-start space-x-3">
                                    <div class="bg-blue-100 p-4 flex justify-center items-center rounded-full">
                                        <i class="pi pi-phone text-primary"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-800 text-xl mb-0">Hotline</h3>
                                        <p class="text-gray-600 text-sm">{{ settings.phone || "0334262754" }}</p>
                                    </div>
                                </div>

                                <!-- Email -->
                                <div class="flex items-start space-x-3">
                                    <div class="bg-blue-100 p-4 flex justify-center items-center rounded-full">
                                        <i class="pi pi-envelope text-primary"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-800 text-xl mb-0">Email</h3>
                                        <p class="text-gray-600 text-sm">{{ settings.email || "admin@gmail.com" }}</p>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </Card>

                    <!-- Contact Form Card -->
                    <Card class="shadow-lg">
                        <template #header>
                            <div class="bg-primary p-2 rounded-t-lg flex items-center justify-center">
                                <h2 class="text-xl  text-white font-bold mb-0">Liên hệ với chúng tôi</h2>
                            </div>
                        </template>
                        <template #content>
                            <div class="p-4">
                                <p class="text-gray-600 mb-6 text-sm">
                                    Nếu bạn có thắc mắc gì, có thể gửi yêu cầu cho chúng tôi, và chúng tôi sẽ liên lạc
                                    lại
                                    với bạn sớm nhất có thể.
                                </p>

                                <form @submit.prevent="submitForm" class="space-y-4">
                                    <!-- Name Field -->
                                    <div class="field">
                                        <InputText v-model="form.name" placeholder="Họ và tên"
                                            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            :class="{ 'border-red-500': errors.name }" />
                                        <small v-if="errors.name" class="text-red-500">{{ errors.name }}</small>
                                    </div>

                                    <!-- Email Field -->
                                    <div class="field">
                                        <InputText v-model="form.email" type="email" placeholder="Email"
                                            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            :class="{ 'border-red-500': errors.email }" />
                                        <small v-if="errors.email" class="text-red-500">{{ errors.email }}</small>
                                    </div>

                                    <!-- Phone Field -->
                                    <div class="field">
                                        <InputText v-model="form.phone" placeholder="Điện thoại"
                                            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            :class="{ 'border-red-500': errors.phone }" />
                                        <small v-if="errors.phone" class="text-red-500">{{ errors.phone }}</small>
                                    </div>

                                    <!-- Message Field -->
                                    <div class="field">
                                        <Textarea v-model="form.message" placeholder="Nội dung" rows="4"
                                            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                            :class="{ 'border-red-500': errors.message }" />
                                        <small v-if="errors.message" class="text-red-500">{{ errors.message }}</small>
                                    </div>

                                    <!-- Submit Button -->
                                    <Button type="submit" label="Gửi thông tin" :loading="isSubmitting"
                                        class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200" />
                                </form>
                            </div>
                        </template>
                    </Card>
                </div>

                <!-- Right Panel - Map -->
                <div class="lg:sticky lg:top-4">
                    <div class="shadow-lg h-full">
                        <div class="p-0 h-full min-h-[600px]">
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d501725.41842284494!2d106.36556364999999!3d10.755292999999999!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x317529292e8d3dd1%3A0xf15f5aad773c112b!2sHo%20Chi%20Minh%20City%2C%20Vietnam!5e0!3m2!1sen!2s!4v1699999999999!5m2!1sen!2s"
                                width="100%" height="100%" style="border:0; border-radius: 0.5rem;" allowfullscreen=""
                                loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                                class="min-h-[600px]"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ClientLayout>
</template>
