<script setup>
import { ref, onMounted, reactive, onUnmounted } from "vue";
import ProductCard from "./ProductCard.vue";

const props = defineProps({
    productSale: Object
})

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
        numVisible: 2,
        numScroll: 1
    }
]);
const countdown = reactive({
    hours: "00",
    minutes: "00",
    seconds: "00",
});

let timer = null;
onMounted(() => {
    const updateCountdown = () => {
        const now = new Date();
        const endOfDay = new Date();
        endOfDay.setHours(23, 59, 59, 999);

        let totalSeconds = Math.floor((endOfDay.getTime() - now.getTime()) / 1000);

        if (totalSeconds <= 0) {
            if (timer) clearInterval(timer);
            totalSeconds = 0;
        }
        countdown.hours = String(Math.floor(totalSeconds / 3600)).padStart(2, "0");
        countdown.minutes = String(Math.floor((totalSeconds % 3600) / 60)).padStart(2, "0");
        countdown.seconds = String(totalSeconds % 60).padStart(2, "0");
    };

    updateCountdown();
    timer = setInterval(updateCountdown, 1000);
});

onUnmounted(() => {
    if (timer) clearInterval(timer);
});
</script>
<template>
    <div class="max-w-7xl px-4 m-auto rounded-2xl">
        <div class="bg-white p-4 md:p-6 rounded-2xl shadow-md">
            <div class="flex mb-0 pb-2 justify-between flex-wrap relative">
                <h3 class="border-b-4 border-primary inline-block w-full">
                    <span class="text-2xl px-6 py-2 font-bold section-title-main bg-primary text-white">
                        Sản phẩm giá ưu đãi
                    </span>
                </h3>
                <div
                    class="flex items-center space-x-3 px-6 py-2 rounded-xl relative right-0 -top-3 md:absolute bg-white shadow">
                    <span class="font-semibold">Kết thúc trong:</span>
                    <div class="flex space-x-2 font-mono text-xl font-bold">
                        <span class="bg-primary text-white px-2 py-1 rounded-md shadow">
                            {{ countdown.hours }}
                        </span>
                        <span>:</span>
                        <span class="bg-primary text-white px-2 py-1 rounded-md shadow">
                            {{ countdown.minutes }}
                        </span>
                        <span>:</span>
                        <span class="bg-primary text-white px-2 py-1 rounded-md shadow">
                            {{ countdown.seconds }}
                        </span>
                    </div>
                </div>

            </div>
            <div class="relative">
                <Carousel :value="productSale" :numVisible="4" :numScroll="1" :responsiveOptions="responsiveOptions"
                    circular :autoplayInterval="3000">
                    <template #item="slotProps">
                        <ProductCard :product="slotProps.data" is_m />
                    </template>
                </Carousel>
            </div>

        </div>
    </div>
</template>
<style>
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
