<script setup>
import { Link, usePage } from "@inertiajs/vue3"
import { ref, onMounted, onUnmounted, watch } from "vue"
import Logo from "../Logo.vue"
import SearchProduct from "./SearchProduct.vue"

const { categories, settings } = usePage().props;
const cartCount = ref(0)
const showMobileMenu = ref(false)
const showCategoryMenu = ref(false)
const isScrolled = ref(false)
const page = usePage();

const navigationItems = ref([
    { name: "Trang chủ", url: '/', icon: 'pi pi-home' },
    { name: "Sản phẩm", url: '/san-pham', icon: 'pi pi-box' },
    { name: "Tin tức", url: '/tin-tuc', icon: 'pi pi-book' },
    { name: "Liên hệ", url: '/lien-he', icon: 'pi pi-phone' }
])


const toggleCategoryMenu = () => {
    if (page.url === '/') return;
    showCategoryMenu.value = !showCategoryMenu.value
}

const handleScroll = () => {
    isScrolled.value = window.scrollY > 100
}

onMounted(() => {
    window.addEventListener("scroll", handleScroll)
})
onUnmounted(() => {
    window.removeEventListener("scroll", handleScroll)
})
</script>

<template>
    <header class="fixed top-0 left-0 w-full z-50">
        <div class="bg-white shadow transition-all duration-500">
            <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <Logo />
                </div>

                <!-- Search box -->
                <SearchProduct />

                <div class="hidden md:flex items-center space-x-6">
                    <a href="#" class="text-primary hover:text-primary-800 font-bold text-sm flex items-center">
                        <i class="pi pi-user mr-1"></i> Đăng nhập
                    </a>
                    <span class="text-gray-300">|</span>
                    <a href="#" class="text-primary hover:text-primary-800 font-bold text-sm">Đăng ký</a>
                    <div class="relative">
                        <Button icon="pi pi-shopping-cart" class="p-button-text text-primary relative" />
                        <Badge :value="cartCount" severity="warning" class="absolute -top-2 -right-2" />
                    </div>
                </div>

                <!-- Nút menu mobile -->
                <button class="md:hidden text-primary text-2xl" @click="showMobileMenu = !showMobileMenu">
                    <i class="pi pi-bars"></i>
                </button>
            </div>
        </div>

        <!-- Navigation Bar -->
        <transition name="slide-down">
            <div v-show="isScrolled || !isScrolled" class="bg-primary text-white shadow">
                <div class="max-w-7xl mx-auto px-4 flex items-center justify-between">
                    <div class="flex items-center justify-between w-full">
                        <div class="relative">
                            <Button icon="pi pi-bars" label="DANH MỤC SẢN PHẨM" class=" px-6 py-3 rounded-none"
                                @click="toggleCategoryMenu" />
                            <!-- Category Dropdown Menu -->
                            <transition name="slide-fade">
                                <div v-if="showCategoryMenu"
                                    class="absolute top-full left-0 right-0  w-[240px] rounded-lg p-2 bg-white text-gray-600 shadow-lg z-40 border-t">
                                    <ul class="space-y-3 max-h-[300px] overflow-y-auto custom-scrollbar">
                                        <li v-for="cat in categories" :key="cat.id"
                                            class="flex items-center justify-between rounded-lg hover:bg-primary hover:text-white cursor-pointer transition">
                                            <Link :href="route('products.category', cat.slug)"
                                                class="flex-1 px-3 py-2 ">
                                            <span>{{ cat.name }}</span>
                                            </Link>
                                        </li>
                                    </ul>
                                </div>
                            </transition>
                        </div>
                        <nav class="hidden md:flex items-center space-x-8">
                            <Link v-for="item in navigationItems" :key="item.name" :href="item.url"
                                class="py-4 px-2 transition font-bold" :class="{
                                    'text-orange-300 border-b-2 border-orange-300': usePage().url === item.url,
                                    'hover:text-orange-300': usePage().url !== item.url
                                }">
                            {{ item.name }}
                            </Link>
                        </nav>
                        <div class="flex items-center space-x-2 text-white">
                            <i class="pi pi-phone text-lg"></i>
                            <a :href="`tel:${settings.phone || '0334262754'}`" class="font-bold text-lg">{{
                                settings.phone || '0334262754' }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </transition>
        <!-- menu mobile -->
        <Drawer v-model:visible="showMobileMenu" position="right">
            <!-- Header -->
            <template #header>
                <div class="flex items-center w-full ">
                    <Logo />
                </div>

            </template>
            <!-- Navigation Items -->
            <nav class="flex-1 space-y-2">
                <Link v-for="item in navigationItems" :key="item.url" :href="item.url" @click="closeMenu" :class="[
                    'flex items-center gap-3 px-4 py-2 rounded-lg transition-colors duration-150',
                    page.url === item.url
                        ? 'bg-gray-200 text-primary font-semibold'
                        : 'text-gray-700 hover:bg-gray-100 hover:text-primary'
                ]">
                <i :class="[item.icon, 'text-lg']"></i>
                <span class="text-base">{{ item.name }}</span>
                </Link>
            </nav>
        </Drawer>
    </header>
</template>
<style>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px !important;
    height: 6px !important;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #e5e7eb;
    border-radius: 4px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}

.custom-scrollbar {
    scrollbar-width: thin;
    scrollbar-color: var(--primary-color) transparent;
}

.banner-carousel .p-carousel-indicator-list {
    position: absolute !important;
    bottom: -5px !important;
    left: 50%;
    transform: translateX(-50%);
}

.p-carousel-content-container,
.p-carousel-content {
    height: 100% !important;
    overflow: hidden !important;
}
</style>