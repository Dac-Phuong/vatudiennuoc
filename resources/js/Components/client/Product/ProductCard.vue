<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { ref } from 'vue'

const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
    is_m: {
        type: Boolean,
    }
})
const { settings } = usePage().props;
const showDialog = ref(false)

const responsiveOptions = ref([
    {
        breakpoint: '1300px',
        numVisible: 4
    },

]);

</script>
<template>
    <div class="bg-white rounded-lg shadow-sm border hover:shadow-md transition-shadow duration-200"
        :class="is_m ? 'm-1 md:m-2' : ''">
        <div class="relative">
            <Image :src="product?.thumbnail" :alt="product?.name" class="w-full object-cover prd-image rounded-t-lg" />
            <div class="absolute top-2 right-2 flex gap-2">
                <Button icon="pi pi-eye" severity="secondary" rounded outlined size="small" @click="showDialog = true"
                    class="bg-white/80 backdrop-blur-sm" />
            </div>
            <div v-if="product?.discount" class="absolute top-2 left-2">
                <span class="bg-red-500 text-white text-xs px-2 py-1 rounded">
                    -{{ product.discount }}%
                </span>
            </div>
        </div>

        <div class="px-4 pb-3 pt-1">
            <h3 class="font-semibold text-gray-900 mb-1 line-clamp-2 text-base">{{ product?.name
            }}</h3>
            <p class="text-sm text-primary mb-1">{{ product?.category?.name }}</p>
            <div class="flex items-center justify-between">
                <div v-if="product?.price > 0" class="flex items-center gap-2">
                    <span class="text-lg font-bold text-emerald-600">
                        {{ product.price }}
                    </span>
                </div>
                <span v-else class="text-sm text-red-500 mt-0 block text-center">Liên hệ</span>
            </div>
            <Link :href="route('products.detail', product?.slug || '')" class="mt-3 block">
            <Button label="Xem chi tiết" icon="pi pi-eye" size="small"
                class="w-full mt-1 bg-blue-500 border-blue-500 hover:bg-blue-600" />
            </Link>
        </div>
    </div>
    <Dialog v-model:visible="showDialog" header="" modal :style="{ width: '60rem' }" class="custom-dialog">
        <div class="flex flex-col md:flex-row gap-6 py-4">
            <div class="flex-shrink-0 flex justify-center items-center h-full w-full md:w-1/2">
                <Image v-if="product.images && product.images?.length === 0" preview :src="product.thumbnail"
                    :alt="product.name" class="w-full h-[450px] object-cover shadow-lg border img-fluid" />
                <Galleria v-else :value="product.images" :responsiveOptions="responsiveOptions" :numVisible="5"
                    :circular="true" :showItemNavigators="true" :showThumbnails="true" class="w-full h-[450px]">
                    <template #item="slotProps">
                        <img :src="slotProps.item.image_path" :alt="slotProps.item?.alt"
                            style="width: 100%; height: 350px;" />
                    </template>
                    <template #thumbnail="slotProps">
                        <img :src="slotProps.item.image_path" :alt="slotProps.item?.alt" style="height: 100px;" />
                    </template>
                </Galleria>
            </div>

            <div class="flex flex-col justify-between w-full md:w-1/2">
                <div>
                    <h2 class="text-2xl font-bold mb-4 text-gray-800">{{ product.name }}</h2>
                    <div class="flex items-center gap-2 mb-3">
                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-medium">
                            Còn hàng
                        </span>

                    </div>
                    <div class="mb-4 text-gray-700 text-sm leading-relaxed">
                        <strong class="block mb-1 text-gray-900">Mô tả sản phẩm:</strong>
                        <span>{{ product.short_desc || 'Chưa có mô tả cho sản phẩm này.' }}</span>
                    </div>
                    <div class="mb-4">
                        <span class="text-lg font-bold text-red-500">Giá: Liên hệ</span>
                    </div>
                    <div class="mt-4 flex flex-col gap-2">
                        <a :href="`tel:${settings.phone}`"
                            class="bg-blue-500 hover:bg-blue-600 text-white py-2 rounded-lg text-center font-semibold transition">
                            <i class="pi pi-phone mr-2"></i>Gọi ngay: {{ settings.phone }}
                        </a>
                        <a :href="`mailto:${settings.email}`"
                            class="bg-gray-100 hover:bg-gray-200 text-gray-800 py-2 rounded-lg text-center font-medium transition">
                            <i class="pi pi-envelope mr-2"></i>Gửi email: {{ settings.email }}
                        </a>
                        <a :href="`${settings.link_zalo}`" target="_blank"
                            class="text-white py-2 bg-primary rounded-lg text-center font-semibold transition">
                            <i class="pi pi-comments mr-2"></i>Liên hệ zalo
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </Dialog>

</template>

<style>
.custom-dialog .p-dialog-header {
    position: relative !important;
    padding: 5px;
}

.custom-dialog .p-dialog-header-actions {
    position: absolute;
    top: 0.5rem !important;
    right: 0.5rem !important;
    z-index: 10 !important;
}


span.p-image.p-component.w-full.object-cover.shadow-lg.border.img-fluid img {
    width: 100% !important;
}

.p-card-body {
    padding: 0 !important;
}

.prd-image img {
    height: 180px !important;
    width: 100% !important;
}
</style>