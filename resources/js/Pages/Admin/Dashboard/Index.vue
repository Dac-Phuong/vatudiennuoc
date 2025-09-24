<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useLayout } from '@/Layouts/Composables/layout';
import { Head } from '@inertiajs/vue3';
import { onMounted, ref, watch } from 'vue';

const props = defineProps({
    data: Array,
})
const { getPrimary, getSurface, isDarkTheme } = useLayout();
const lineData = ref(null);
const lineOptions = ref(null);
const money = ref([650000, 590000, 800000, 810000, 560000, 550000, 400000, 450000, 600000, 700000, 750000, 10000000])
onMounted(() => {
    setColorOptions();
});
function setColorOptions() {
    const documentStyle = getComputedStyle(document.documentElement);
    lineData.value = {
        labels: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'],
        datasets: [
            {
                label: 'Doanh thu',
                data: money.value,
                fill: false,
                backgroundColor: documentStyle.getPropertyValue('--p-primary-500'),
                borderColor: documentStyle.getPropertyValue('--p-primary-500'),
                tension: 0.4
            },

        ]
    };

}

watch(
    [getPrimary, getSurface, isDarkTheme],
    () => {
        setColorOptions();
    },
    { immediate: true }
);
</script>


<template>

    <Head title="Thống kê" />
    <AppLayout>
        <div class="max-w-8xl mx-auto p-0">
            <div class="overflow-hidden sm:rounded-lg">
                <div class="grid grid-cols-12 gap-8">
                    <div class="col-span-12 lg:col-span-6 xl:col-span-3">
                        <div class="card mb-0">
                            <div class="flex justify-between mb-4">
                                <div>
                                    <span class="block text-muted-color font-medium mb-4">Đơn hàng</span>
                                    <div class="text-surface-900 dark:text-surface-0 font-medium text-xl">152</div>
                                </div>
                                <div class="flex items-center justify-center bg-blue-100 dark:bg-blue-400/10 rounded-border"
                                    style="width: 2.5rem; height: 2.5rem">
                                    <i class="pi pi-shopping-cart text-blue-500 !text-xl"></i>
                                </div>
                            </div>
                            <span class="text-primary font-medium">24 đơn mới </span>
                            <span class="text-muted-color">chưa xem</span>
                        </div>
                    </div>
                    <div class="col-span-12 lg:col-span-6 xl:col-span-3">
                        <div class="card mb-0">
                            <div class="flex justify-between mb-4">
                                <div>
                                    <span class="block text-muted-color font-medium mb-4">Doanh thu</span>
                                    <div class="text-surface-900 dark:text-surface-0 font-medium text-xl">$2.100
                                    </div>
                                </div>
                                <div class="flex items-center justify-center bg-orange-100 dark:bg-orange-400/10 rounded-border"
                                    style="width: 2.5rem; height: 2.5rem">
                                    <i class="pi pi-dollar text-orange-500 !text-xl"></i>
                                </div>
                            </div>
                            <span class="text-primary font-medium">%52+ </span>
                            <span class="text-muted-color">so với tuần trước</span>
                        </div>
                    </div>
                    <div class="col-span-12 lg:col-span-6 xl:col-span-3">
                        <div class="card mb-0">
                            <div class="flex justify-between mb-4">
                                <div>
                                    <span class="block text-muted-color font-medium mb-4">Khách hàng</span>
                                    <div class="text-surface-900 dark:text-surface-0 font-medium text-xl">28441
                                    </div>
                                </div>
                                <div class="flex items-center justify-center bg-cyan-100 dark:bg-cyan-400/10 rounded-border"
                                    style="width: 2.5rem; height: 2.5rem">
                                    <i class="pi pi-users text-cyan-500 !text-xl"></i>
                                </div>
                            </div>
                            <span class="text-primary font-medium">520 </span>
                            <span class="text-muted-color">đã đăng ký mới</span>
                        </div>
                    </div>
                    <div class="col-span-12 lg:col-span-6 xl:col-span-3">
                        <div class="card mb-0">
                            <div class="flex justify-between mb-4">
                                <div>
                                    <span class="block text-muted-color font-medium mb-4">Bình luận</span>
                                    <div class="text-surface-900 dark:text-surface-0 font-medium text-xl">152 Chưa đọc
                                    </div>
                                </div>
                                <div class="flex items-center justify-center bg-purple-100 dark:bg-purple-400/10 rounded-border"
                                    style="width: 2.5rem; height: 2.5rem">
                                    <i class="pi pi-comment text-purple-500 !text-xl"></i>
                                </div>
                            </div>
                            <span class="text-primary font-medium">85 </span>
                            <span class="text-muted-color">đã phản hồi</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="max-w-8xl mx-auto mt-8">
            <div class="card">
                <div class="font-semibold text-xl mb-4 w-full">Thông kê doanh thu</div>
                <Chart type="line" height="100px" :data="lineData" :options="lineOptions">
                </Chart>
            </div>
        </div>
    </AppLayout>
</template>
