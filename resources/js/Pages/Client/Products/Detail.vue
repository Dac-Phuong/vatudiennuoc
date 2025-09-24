<script setup>
import { ref, computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import ClientLayout from '@/Layouts/ClientLayout.vue';
import ProductCard from '@/Components/client/Product/ProductCard.vue';
import Breadcrumbs from '@/Components/client/Breadcrumbs.vue';


const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
    relatedProducts: {
        type: Array,
        required: false,
        default: () => []
    }
})
const { settings } = usePage().props;
// Product data
const responsiveOptions = ref([
    {
        breakpoint: '1400px',
        numVisible: 4,
        numScroll: 1
    },
    {
        breakpoint: '1199px',
        numVisible: 3,
        numScroll: 1
    },
    {
        breakpoint: '767px',
        numVisible: 2,
        numScroll: 1
    },
    {
        breakpoint: '575px',
        numVisible: 1,
        numScroll: 1
    }
]);


const breadcrumbItems = computed(() => [
    {
        label: props.product.category.name,
        route: route('products.category', props.product.category.slug)
    },
    {
        label: props.product.name,
    },
])

</script>
<template>

    <Head :title="product.name">
        <meta name="description" :content="product.description || 'Chi tiết sản phẩm ' + product.name" />
        <meta property="og:title" :content="product.seo[0]?.meta_title || product.name" />
        <meta property="og:description"
            :content="product.seo[0]?.meta_description || 'Chi tiết sản phẩm ' + product.name" />
        <meta property="og:image" :content="product.thumbnail" />
        <meta property="og:url" :content="route('products.detail', product.id)" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="product.seo[0]?.meta_title || product.name" />
        <meta name="twitter:description"
            :content="product.seo[0]?.meta_description || 'Chi tiết sản phẩm ' + product.name" />
        <meta name="twitter:image" :content="product.thumbnail" />
        <meta name="keywords" :content="product.seo[0]?.meta_keywords || ''" />
    </Head>
    <ClientLayout>
        <div class="min-h-screen bg-gray-100 pb-5 ">
            <!-- Breadcrumb -->
            <Breadcrumbs :items="breadcrumbItems" />
            <div class="max-w-7xl mx-auto px-4 py-6 mt-4 bg-white rounded-md shadow-md">
                <!-- Main Product Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                    <!-- Image Gallery -->
                    <div class="space-y-3">
                        <!-- Main Image -->
                        <div class="aspect-square h-[400px] w-full rounded-lg overflow-hidden border border-border">
                            <Image :src="product.thumbnail" preview :alt="product.name"
                                class="w-full prod-image h-full object-cover hover:scale-105 transition-transform duration-300" />
                        </div>
                        <!-- Thumbnails -->
                        <div class="flex gap-2" v-if="product.images && product.images.length">
                            <div v-for="(image, index) in product.images" :key="index"
                                @click="product.thumbnail = image.image_path" :class="[
                                    'w-24 h-24 rounded-md overflow-hidden border-2 cursor-pointer transition-all duration-200',
                                    product.thumbnail === image.image_path ? 'border-primary' : 'border-border hover:border-accent'
                                ]">
                                <Image :src="image.image_path" :alt="`${product.name} ${index + 1}`"
                                    class="w-full h-full object-cover" />
                            </div>
                        </div>
                    </div>

                    <!-- Product Info -->
                    <div class="space-y-6">
                        <div class="space-y-3">
                            <div>
                                <h2 class="text-2xl font-bold mb-4 text-gray-800">{{ product.name }}</h2>
                                <div class="flex items-center gap-2 mb-3">
                                    <Tag severity="info">
                                        {{ product.brand?.name || "chưa cập nhật" }}
                                    </Tag>
                                    |
                                    <Tag class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-medium">
                                        Còn hàng
                                    </Tag>

                                </div>
                                <div class="mb-4 text-gray-700 text-sm leading-relaxed">
                                    <strong class="block mb-1 text-gray-900">Mô tả sản phẩm:</strong>
                                    <span>{{ product.description || 'Chưa có mô tả cho sản phẩm này.' }}</span>
                                </div>
                                <div class="mb-4">
                                    <span class="text-lg font-bold text-red-500">Giá: Liên hệ</span>
                                </div>
                                <div class="mt-4 flex flex-col gap-2">
                                    <a :href="`tel:${settings?.phone}`"
                                        class="bg-blue-500 hover:bg-blue-600 text-white py-2 rounded-lg text-center font-semibold transition">
                                        <i class="pi pi-phone mr-2"></i>Gọi ngay: {{ settings?.phone }}
                                    </a>
                                    <a :href="`mailto:${settings?.email}`"
                                        class="bg-gray-100 hover:bg-gray-200 text-gray-800 py-2 px-4 rounded-lg text-center font-medium transition inline-flex items-center">
                                        <span class="m-auto"><i class="pi pi-envelope mr-2"></i>
                                            Gửi email: {{ settings?.email }}</span>
                                    </a>

                                    <a :href="settings?.link_zalo" target="_blank" rel="noopener"
                                        class="text-white py-2 px-4 bg-primary rounded-lg text-center font-semibold transition hover:bg-primary/90 inline-flex items-center">
                                        <span class="m-auto">
                                            <i class="pi pi-comments mr-2"></i>
                                            Liên hệ zalo
                                        </span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Product Details Tabs -->
            <div class="max-w-7xl mx-auto p-4 mt-5 bg-white rounded-md shadow-md">
                <TabView>
                    <TabPanel header="Mô tả chi tiết">
                        <div class="prose max-w-none text-muted-foreground">
                            <h3 class="border-b-4 border-primary inline-block w-full">
                                <span class="text-2xl px-6 py-2 font-bold section-title-main bg-primary text-white">
                                    Mô tả sản phẩm
                                </span>
                            </h3>
                            <p v-html="product.content" class="content-clamp"></p>
                        </div>
                    </TabPanel>
                </TabView>
            </div>
            <!-- Related Products -->
            <div class="max-w-7xl mx-auto p-4 mt-5 bg-white rounded-md shadow-md">
                <h3 class="border-b-4 border-primary inline-block w-full">
                    <span class="text-2xl px-6 py-2 font-bold section-title-main bg-primary text-white">
                        Sản phẩm cùng loại
                    </span>
                </h3>
                <div class="mt-1 relative">
                    <Carousel :value="relatedProducts" :numVisible="4" :numScroll="1"
                        :responsiveOptions="responsiveOptions" circular :autoplayInterval="3000">
                        <template #item="slotProps">
                            <ProductCard :product="slotProps.data" is_m />
                        </template>
                    </Carousel>
                </div>
            </div>
        </div>
    </ClientLayout>
</template>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.prose {
    line-height: 1.6;
}

.section-title-main {
    text-transform: uppercase;
    font-size: 16px;
    line-height: 16px;
    color: #fff;
    border-radius: 5px 5px 0 0;
    padding: 8px 20px;
    font-weight: bold;
    position: relative;
    margin-right: 20px;
}


.section-title-main:after {
    content: '';
    display: block;
    line-height: 35px;
    background: var(--primary-color);
    width: 18px;
    height: 35px;
    position: absolute;
    top: 0;
    right: 0;
    margin-right: -7px;
    transform: skewX(21deg);
    border-radius: 0 5px 0 0;
}

.content-clamp {
    word-break: break-word;
    overflow: hidden;
    line-height: 20px;
}

.prod-image img {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
    transition: transform 0.3s ease;
}

.p-carousel-content .p-button-text.p-button-secondary {
    position: absolute !important;
}

.p-carousel-content .p-carousel-prev-button {
    left: 10px !important;
    z-index: 10 !important;
    background-color: #fff !important;
}

.p-carousel-content .p-carousel-next-button {
    right: 10px !important;
    z-index: 10 !important;
    background-color: #fff !important;
}
</style>