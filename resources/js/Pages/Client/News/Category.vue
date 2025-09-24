<script setup>
import Breadcrumbs from '@/Components/client/Breadcrumbs.vue';
import ClientLayout from '@/Layouts/ClientLayout.vue'
import formatDate from '@/utils/formatDate';
import { formatViews } from '@/utils/formatNumber';
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
    news: Object,
    featuredNews: Object,
    newCategory: Array,
    category: Object
})

const breadcrumbItems = ref([
    { label: 'Tin tức', route: '/tin-tuc' },
    { label: props.category.name }
])

const onPageChange = ({ page, rows }) => {
    router.get(route('news.category', { slug: props.category.slug }), {
        page: page + 1,
        per_page: rows
    }, { preserveScroll: true, preserveState: true })
}
</script>

<template>

    <Head :title="category.name" />
    <ClientLayout>
        <div class="min-h-screen bg-gray-100">
            <!-- Breadcrumb -->
            <Breadcrumbs :items="breadcrumbItems" class="text-sm" />

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Content -->
                    <div class="lg:col-span-2 order-2">
                        <div v-for="item in news.data" :key="item.id"
                            class=" card shadow-lg rounded-lg overflow-hidden hover:shadow-xl transition">
                            <div class="flex flex-col md:flex-row">
                                <!-- Ảnh bên trái -->
                                <div class="md:w-1/3">
                                    <Link :href="route('news.detail', { slug: item.slug })">
                                    <img :src="item.thumbnail" alt=""
                                        class="rounded-lg w-full h-48 md:h-full object-cover">
                                    </Link>
                                </div>

                                <!-- Nội dung bên phải -->
                                <div class="md:w-2/3 p-4 flex flex-col justify-between">
                                    <div>
                                        <Link :href="route('news.detail', { slug: item.slug })">
                                        <h2 class="text-xl font-semibold text-gray-900 hover:text-primary transition">
                                            {{ item.title }}
                                        </h2>
                                        </Link>
                                        <p class="mt-2 text-sm text-gray-600 line-clamp-3">
                                            {{ item.short_description }}
                                        </p>
                                    </div>

                                    <div class="mt-4 flex items-center justify-between text-sm text-gray-500">
                                        <div class=" gap-2">
                                            <span>Tác giả: {{ item.author?.full_name }}</span>
                                            <br>
                                            <span>lượt xem: {{ formatViews(item.views) }}</span>
                                        </div>
                                        <span>{{ formatDate(item.created_at) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center py-5" v-if="news.data.length === 0">Không có dữ liệu</div>
                        <Paginator v-if="news.data.length > 0" :first="(news.current_page - 1) * news.per_page"
                            :rows="news.per_page" :totalRecords="news.total" :rowsPerPageOptions="[9, 18, 36]"
                            template="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
                            @page="onPageChange" />
                    </div>


                    <!-- Sidebar -->
                    <div class="space-y-6 order-1">
                        <!-- Author Info -->
                        <div class="card shadow-lg">
                            <div>
                                <h2 class="text-xl">Danh mục tin tức</h2>
                                <ul class="space-y-2 mt">
                                    <li v-for="item in newCategory" :key="item.id">
                                        <Link :href="route('news.category', { slug: item.slug })"
                                            :class="category.id == item.id ? 'bg-primary text-white' : ''"
                                            class="flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-200 hover:bg-primary hover:text-white transition duration-200">
                                        <i class="pi pi-folder group-hover:text-white"></i>
                                        <span class="font-medium">{{ item.name }}</span>
                                        <span class="ml-auto text-xs group-hover:text-gray-200">
                                            {{ item.news_count || 0 }} bài
                                        </span>
                                        </Link>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <!-- Related Articles -->
                        <div class="card shadow-lg">
                            <h2 class="text-xl">Bài viết nổi bật</h2>
                            <div class="space-y-4">
                                <div v-for="news in featuredNews" :key="news.id"
                                    class="flex space-x-3 p-1 rounded-lg hover:bg-gray-50 transition-colors">
                                    <img :src="news.thumbnail" :alt="news?.title"
                                        class="w-16 h-16 object-cover rounded-lg flex-shrink-0" />
                                    <Link :href="route('news.detail', { slug: news.slug })" class="flex-1 min-w-0">
                                    <h5 class="font-medium hover:text-primary text-sm text-gray-900 line-clamp-2 mb-1">
                                        {{ news?.title }}
                                    </h5>
                                    <p class="text-xs text-gray-600">{{ formatDate(news.created_at) }}</p>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ClientLayout>
</template>


<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.card {
    padding: 10px !important;
    margin-bottom: 10px !important;
}
</style>