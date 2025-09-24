<script setup>
import formatDate from '@/utils/formatDate';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    firstNews: Object,
    news: Array,
})
</script>
<template>
    <section class="relative overflow-hidden">
        <div class="max-w-7xl mx-auto p-4 mb-10 bg-white shadow-lg rounded-xl">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h3 class="border-b-4 border-primary inline-block w-full">
                    <span class="text-2xl px-6 py-2 font-bold section-title-main bg-primary text-white">
                        Tin tức mới nhất
                    </span>
                </h3>
            </div>
            <!-- Modified grid layout to make sidebar wider (2 columns instead of 1) -->
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
                <!-- Main Featured Article -->
                <div class="lg:col-span-3" v-if="firstNews">
                    <Card class="overflow-hidden shadow-lg">
                        <template #content>
                            <!-- Ảnh -->
                            <div class="relative">
                                <img :src="firstNews.thumbnail" :alt="firstNews.title"
                                    class="w-full h-96 object-cover rounded-lg" />
                            </div>
                            <!-- Nội dung -->
                            <div class="p-4">
                                <h2 class="text-xl font-bold text-gray-800 mb-3 leading-tight">
                                    {{ firstNews.title }}
                                </h2>

                                <div class="flex items-center gap-4 text-sm text-gray-600 mb-3">
                                    <span class="font-medium text-blue-600">
                                        {{ firstNews.author?.full_name || 'Không rõ tác giả' }}
                                    </span>
                                    <span>{{ formatDate(firstNews.created_at) }}</span>
                                </div>

                                <p class="text-gray-700 leading-relaxed">
                                    {{ firstNews.short_description }}
                                    <a :href="`/tin-tuc/${firstNews.slug}`"
                                        class="text-orange-500 hover:text-orange-600 ml-1">
                                        Đọc tiếp
                                    </a>
                                </p>
                            </div>
                        </template>
                    </Card>
                </div>

                <div class="lg:col-span-2">
                    <div class="h-[470px] overflow-y-auto space-y-4 custom-scrollbar">
                        <Card v-for="article in news" :key="article.id"
                            class="overflow-hidden shadow-md hover:shadow-lg transition-shadow flex-shrink-0">
                            <template #content>
                                <Link :href="route('news.detail', { slug: article.slug })" class="flex gap-4 p-2">
                                <div class="flex-shrink-0">
                                    <img :src="article.thumbnail" :alt="article.title"
                                        class="w-24  h-24 object-cover rounded-lg" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-bold text-gray-800 text-sm leading-tight mb-2 line-clamp-2">
                                        {{ article.title }}
                                    </h3>
                                    <div class="flex items-center gap-2 text-sm text-gray-600 mb-2">
                                        <span class="font-medium text-blue-600">{{ article.author?.full_name }}</span>
                                        <span>{{ formatDate(article.created_at) }}</span>
                                    </div>
                                    <p class="text-xs text-gray-700 leading-relaxed line-clamp-2">
                                        {{ article.short_description }}
                                    </p>
                                </div>
                                </Link>
                            </template>
                        </Card>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>


<style>
.custom-scrollbar::-webkit-scrollbar {
    height: 6px !important;
    width: 3px !important;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 9999px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}

.custom-scrollbar {
    scrollbar-width: thin;
    scrollbar-color: var(--primary-color) transparent;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
