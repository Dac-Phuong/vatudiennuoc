<script setup>
import { ref } from 'vue';
import Logo from '../Logo.vue';
import { Link, usePage } from '@inertiajs/vue3';
const showTooltip = ref(null)
const { settings, categories } = usePage().props

</script>

<template>
    <!-- Footer -->
    <footer class="bg-white text-gray-500 border-t border-gray shadow-lg pt-14 pb-4">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
                <!-- Company Info -->
                <div>
                    <div class="flex items-center space-x-4 mb-5">
                        <Logo />
                    </div>
                    <p class=" mb-6 leading-relaxed">
                        Chúng tôi cung cấp những sản phẩm chất lượng cao với giá cả hợp lý, mang đến trải nghiệm mua sắm
                        tuyệt vời cho khách hàng.
                    </p>
                    <div class="flex space-x-3">
                        <Button icon="pi pi-facebook"
                            class="p-button-rounded p-button-outlined p-button-sm hover:bg-blue-600 hover:text-white transition" />
                        <Button icon="pi pi-twitter"
                            class="p-button-rounded p-button-outlined p-button-sm hover:bg-primary hover:text-white transition" />
                        <Button icon="pi pi-instagram"
                            class="p-button-rounded p-button-outlined p-button-sm hover:bg-pink-500 hover:text-white transition" />
                    </div>
                </div>
                <!-- Quick Links -->
                <div>
                    <h5 class="text-lg font-semibold mb-5 text-primary uppercase tracking-wider">Liên Kết Nhanh</h5>
                    <ul class="space-y-3">
                        <li>
                            <Link href="/" class="flex items-center  hover:text-primary transition-colors">
                            <i class="pi pi-angle-right mr-2"></i>Trang chủ
                            </Link>
                        </li>
                        <li>
                            <Link href="san-pham" class="flex items-center  hover:text-primary transition-colors">
                            <i class="pi pi-angle-right mr-2"></i>Sản phẩm
                            </Link>
                        </li>
                        <li>
                            <Link href="tin-tuc" class="flex items-center  hover:text-primary transition-colors">
                            <i class="pi pi-angle-right mr-2"></i>Tin tức
                            </Link>
                        </li>
                        <li>
                            <Link href="lien-he" class="flex items-center  hover:text-primary transition-colors">
                            <i class="pi pi-angle-right mr-2"></i>Liên hệ
                            </Link>
                        </li>
                    </ul>
                </div>

                <!-- Categories -->
                <div>
                    <h5 class="text-lg font-semibold mb-5 text-primary uppercase tracking-wider">Danh Mục</h5>
                    <ul class="space-y-3 max-h-52 overflow-y-auto pr-2">
                        <li v-for="item in categories" :key="item.id">
                            <Link :href="route('products.category', item.slug)"
                                class="flex items-center hover:text-primary transition-colors">
                            <i class="pi pi-angle-right mr-2"></i>
                            {{ item.name }}
                            </Link>
                        </li>
                    </ul>

                </div>

                <!-- Contact -->
                <div>
                    <h5 class="text-lg font-semibold mb-5 text-primary uppercase tracking-wider">Liên Hệ</h5>
                    <div class="space-y-4">
                        <div class="flex items-center space-x-3">
                            <i class="pi pi-map-marker text-primary text-lg"></i>
                            <span class="">{{ settings.address }}</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <i class="pi pi-phone text-primary text-lg"></i>
                            <span class="">{{ settings.phone }}</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <i class="pi pi-envelope text-primary text-lg"></i>
                            <span class="">{{ settings.email }}</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <i class="pi pi-clock text-primary text-lg"></i>
                            <span class="">8:00 - 18:00 (T2-T7)</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray mt-5 pt-2 text-center">
                <p class="text-gray-500 text-sm tracking-wide">
                    © 2024 Cty TNHH Thương mại Tổng hợp HTDC. Tất cả quyền được bảo lưu.
                </p>
            </div>
        </div>
        <div class="fixed right-4 top-1/2 transform -translate-y-1/2 z-50 space-y-3">
            <Transition name="slide-right" appear>
                <div class="flex flex-col space-y-3">
                    <!-- Facebook -->
                    <a :href="`${settings.link_facebook || '#'}`"
                        class="group relative w-12 h-12 bg-blue-600 hover:bg-blue-700 rounded-full flex items-center justify-center shadow-lg transition-all duration-300 hover:scale-110 hover:shadow-xl"
                        @mouseenter="showTooltip = 'facebook'" @mouseleave="showTooltip = null">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                        </svg>
                        <div v-show="showTooltip === 'facebook'"
                            class="absolute right-full mr-3 px-2 py-1 bg-gray-800 text-white text-sm rounded whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                            Facebook
                        </div>
                    </a>

                    <!-- Zalo -->
                    <a :href="`${settings.link_zalo || '#'}`" target="_blank"
                        class="group relative w-12 h-12 bg-blue-500 hover:bg-blue-600 rounded-full text-white flex items-center justify-center shadow-lg transition-all duration-300 hover:scale-110 hover:shadow-xl"
                        @mouseenter="showTooltip = 'zalo'" @mouseleave="showTooltip = null">
                        zalo
                        <div v-show="showTooltip === 'zalo'"
                            class="absolute right-full mr-3 px-2 py-1 bg-gray-800 text-white text-sm rounded whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                            Zalo
                        </div>
                    </a>

                    <!-- WhatsApp -->
                    <a href="#"
                        class="group relative w-12 h-12 bg-green-500 hover:bg-green-600 rounded-full flex items-center justify-center shadow-lg transition-all duration-300 hover:scale-110 hover:shadow-xl animate-pulse"
                        @mouseenter="showTooltip = 'whatsapp'" @mouseleave="showTooltip = null">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.465 3.085" />
                        </svg>
                        <div v-show="showTooltip === 'whatsapp'"
                            class="absolute right-full mr-3 px-2 py-1 bg-gray-800 text-white text-sm rounded whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                            WhatsApp
                        </div>
                    </a>

                    <!-- Phone -->
                    <a :href="`tel:${settings?.phone || '#'}`"
                        class="group relative w-12 h-12 bg-red-500 hover:bg-red-600 rounded-full flex items-center justify-center shadow-lg transition-all duration-300 hover:scale-110 hover:shadow-xl animate-pulse"
                        @mouseenter="showTooltip = 'phone'" @mouseleave="showTooltip = null">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <div v-show="showTooltip === 'phone'"
                            class="absolute right-full mr-3 px-2 py-1 bg-gray-800 text-white text-sm rounded whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                            Hotline: {{ settings?.phone }}
                        </div>
                    </a>
                </div>
            </Transition>
        </div>
    </footer>
</template>

<style scoped>
/* Custom animations for floating buttons */
.slide-right-enter-active {
    transition: all 0.6s ease-out;
}

.slide-right-enter-from {
    transform: translateX(-100px);
    opacity: 0;
}

.slide-right-enter-to {
    transform: translateX(0);
    opacity: 1;
}

/* Staggered animation for floating buttons */
.slide-right-enter-active:nth-child(1) {
    transition-delay: 0.1s;
}

.slide-right-enter-active:nth-child(2) {
    transition-delay: 0.2s;
}

.slide-right-enter-active:nth-child(3) {
    transition-delay: 0.3s;
}

.slide-right-enter-active:nth-child(4) {
    transition-delay: 0.4s;
}

/* trong file CSS chính */
::-webkit-scrollbar {
    width: 4px;
}

::-webkit-scrollbar-thumb {
    background: var(--primary-color);
    border-radius: 4px;
}
</style>