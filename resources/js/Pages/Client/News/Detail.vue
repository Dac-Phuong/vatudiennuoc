<script setup>
import Breadcrumbs from '@/Components/client/Breadcrumbs.vue';
import useNotify from '@/Composables/useNotify';
import ClientLayout from '@/Layouts/ClientLayout.vue'
import formatDate from '@/utils/formatDate';
import { Head, Link } from '@inertiajs/vue3'
import { ref } from 'vue'

const notify = useNotify();
const props = defineProps({
    news: Object,
    relatedBySlug: Object,
    newCategory: Array
})

const breadcrumbItems = ref([
    { label: 'Tin tức', route: '/tin-tuc' },
    { label: props.news.title }
])


const shareOnFacebook = () => {
    const url = encodeURIComponent(window.location.href)
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank')
}

const shareOnTwitter = () => {
    const url = encodeURIComponent(window.location.href)
    const text = encodeURIComponent(props.news.title)
    window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, '_blank')
}

const shareOnLinkedIn = () => {
    const url = encodeURIComponent(window.location.href)
    window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${url}`, '_blank')
}

const copyLink = async () => {
    try {
        await navigator.clipboard.writeText(window.location.href)
        notify.success('Copy thành công');
    } catch (err) {
        console.error('Failed to copy link:', err)
    }
}
</script>

<template>

    <Head :title="news.title" />
    <ClientLayout>
        <div class="min-h-screen bg-gray-100">
            <!-- Breadcrumb -->
            <Breadcrumbs :items="breadcrumbItems" class="text-sm" />

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Content -->
                    <div class="lg:col-span-2">
                        <div class="mb-8 card shadow-lg">
                            <!-- Article Header -->
                            <div class="mb-2">
                                <Badge :value="news.category.name"
                                    class="mb-4 bg-orange-100 text-orange-800 px-3 py-1 rounded-full text-sm font-medium" />
                                <h1 class="text-3xl lg:text-4xl text-gray-900 mb-4 mt-0 leading-tight">
                                    {{ news.title }}
                                </h1>

                                <div class="flex items-center justify-between flex-wrap gap-4 mb-6 pb-6 border-b">
                                    <div class="flex items-center space-x-4">
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ news.author.account }}</p>
                                            <p class="text-sm text-gray-600">xin chào mọi người</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center space-x-6 text-sm text-gray-600">
                                        <div class="flex items-center space-x-1">
                                            <i class="pi pi-calendar text-orange-600"></i>
                                            <span>{{ formatDate(news.created_at) }}</span>
                                        </div>
                                        <div class="flex items-center space-x-1">
                                            <i class="pi pi-eye text-orange-600"></i>
                                            <span>{{ news.views }} lượt xem</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Social Share -->
                                <div class="flex items-center space-x-3 mb-6">
                                    <span class="text-sm font-medium text-gray-700">Chia sẻ:</span>
                                    <Button icon="pi pi-facebook" class="p-button-rounded p-button-outlined p-button-sm"
                                        @click="shareOnFacebook" />
                                    <Button icon="pi pi-twitter" class="p-button-rounded p-button-outlined p-button-sm"
                                        @click="shareOnTwitter" />
                                    <Button icon="pi pi-linkedin" class="p-button-rounded p-button-outlined p-button-sm"
                                        @click="shareOnLinkedIn" />
                                    <Button icon="pi pi-copy" class="p-button-rounded p-button-outlined p-button-sm"
                                        @click="copyLink" />
                                </div>
                            </div>

                            <!-- Featured Image -->
                            <div class="mb-8">
                                <img :src="news.thumbnail" :alt="news.title"
                                    class="w-full h-64 lg:h-96 object-cover rounded-lg shadow-lg" />
                                <p v-if="news.imageCaption" class="text-sm text-gray-600 mt-2 italic">{{
                                    news.imageCaption }}</p>
                            </div>
                            <!-- Article Content -->
                            <div class="prose prose-lg max-w-none">
                                <div v-html="news.content" class="text-gray-800 leading-relaxed"></div>
                            </div>
                            <!-- Tags -->
                            <div class="mt-8 pt-6 border-t" v-if="news.tags.length > 0">
                                <div class="flex items-center flex-wrap gap-2">
                                    <span class="text-sm font-medium text-gray-700">Tags:</span>
                                    <Badge v-for="tag in news.tags" :key="tag" :value="tag.name" class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Author Info -->
                        <div class="card shadow-lg">
                            <div>
                                <h2 class="text-xl">Danh mục tin tức</h2>
                                <ul class="space-y-2 mt">
                                    <li v-for="item in newCategory" :key="item.id">
                                        <Link :href="route('news.category', { slug: item.slug })"
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
                            <h2 class="text-xl">Bài viết liên quan</h2>
                            <div class="space-y-4">
                                <div v-for="news in relatedBySlug" :key="news.id"
                                    class="flex space-x-3 p-1 rounded-lg hover:bg-gray-50 transition-colors">
                                    <img :src="news.thumbnail" :alt="news.title"
                                        class="w-16 h-16 object-cover rounded-lg flex-shrink-0" />
                                    <Link :href="route('news.detail', { slug: news.slug })" class="flex-1 min-w-0">
                                    <h5 class="font-medium hover:text-primary text-sm text-gray-900 line-clamp-2 mb-1">
                                        {{ news.title }}
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
    padding: 20px !important;
}
</style>