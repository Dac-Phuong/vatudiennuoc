<script setup>
import { ref, reactive, onMounted } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import FileUploads from '@/Components/admin/FileUploads.vue'
import useNotify from '@/Composables/useNotify.js';
import { generateSlug } from '@/utils/slugify.js';

const props = defineProps({
    categories: Array,
    brands: Array,
    product: Object,
})
const notify = useNotify()
const activeTab = ref(0)
const submitting = ref(false)

const seo = reactive({
    product_id: null,
    meta_title: '',
    meta_description: '',
    meta_keywords: ''
})
const types = [
    { id: 1, name: 'Mới' },
    { id: 2, name: 'Nổi bật' },
    { id: 3, name: 'Bán chạy' },
    { id: 4, name: 'Giảm giá' },
]
const productData = useForm({
    id: null,
    name: '',
    slug: '',
    content: '',
    price: 0,
    discount: 0,
    quantity: 1000,
    thumbnail: null,
    category_id: null,
    brand_id: null,
    type: null,
    seo: null,
    images: []
})

onMounted(() => {
    if (props.product) {
        const { thumbnail, ...rest } = props.product
        Object.assign(productData, rest)
        if (props.product.seo) {
            Object.assign(seo, props.product.seo[0])
        }
    }
})

const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        productData.thumbnail = file;
    }

}

const handleUploaded = (files) => {
    productData.images = files ?? [];
    notify.success('Tải ảnh lên thành công');
}

const submitProduct = () => {
    submitting.value = true
    productData.seo = { ...seo }
    productData.post(route('admin.products.update', props.product.id), {
        onSuccess: () => {
            productData.images = [];
            submitting.value = false;
            notify.success('Cập nhật sản phẩm thành công');
        },
        onError: (errors) => {
            Object.values(errors).forEach(errorArray => {
                notify.error(errorArray)
            });
            submitting.value = false;
        }
    })
}


</script>

<template>

    <Head title="Cập nhật sản phẩm" />
    <AppLayout>
        <div class="grid grid-cols-12 gap-4">
            <div class="col-span-12 lg:col-span-8 card mb-0">
                <div class="pb-6">
                    <h1 class="text-2xl font-bold mb-2">Cập nhật sản Phẩm</h1>
                    <p>Cập nhật thông tin sản phẩm</p>
                </div>

                <TabView v-model:activeIndex="activeTab" class="mt-4">
                    <!-- Tab 1: Thông tin cơ bản -->
                    <TabPanel header="Thông tin cơ bản">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block mb-2">Tên sản phẩm *</label>
                                <InputText v-model="productData.name"
                                    @input="productData.slug = generateSlug(productData.name)" class="w-full"
                                    placeholder="Nhập tên sản phẩm" />
                            </div>
                            <div>
                                <label class="block mb-2">Slug *</label>
                                <InputText v-model="productData.slug"
                                    @input="productData.slug = generateSlug(productData.slug)" class="w-full"
                                    placeholder="slug-san-pham" />
                            </div>

                        </div>
                    </TabPanel>

                    <!-- Tab 2: Chi tiết -->
                    <TabPanel header="Chi tiết sản phẩm">
                        <div class="space-y-4 mt-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block mb-2">Giá *</label>
                                    <InputNumber v-model="productData.price" mode="currency" currency="VND"
                                        locale="vi-VN" class="w-full" />
                                </div>
                                <div>
                                    <label class="block mb-2">Khuyến mãi (%)</label>
                                    <InputNumber v-model="productData.discount" :min="0" :max="100" suffix="%"
                                        class="w-full" />
                                </div>
                                <div>
                                    <label class="block mb-2">Số lượng *</label>
                                    <InputNumber v-model="productData.quantity" :min="0" class="w-full" />
                                </div>
                                <div>
                                    <label class="block mb-2">Danh mục *</label>
                                    <Select v-model="productData.category_id" :options="categories" filter checkmark
                                        optionLabel="name" optionValue="id" placeholder="Chọn danh mục"
                                        class="w-full" />
                                </div>
                                <div>
                                    <label class="block mb-2">Thương hiệu *</label>
                                    <Select v-model="productData.brand_id" filter checkmark :options="brands"
                                        optionLabel="name" optionValue="id" placeholder="Chọn thương hiệu"
                                        class="w-full" />
                                </div>
                                <div>
                                    <label class="block mb-2">Phân loại sản phẩm *</label>
                                    <Select v-model="productData.type" checkmark :options="types" optionLabel="name"
                                        optionValue="id" placeholder="Chọn phân loại" class="w-full" />

                                </div>
                            </div>
                            <div>
                                <label class="block mb-2 mt-4">Nội dung</label>
                                <Editor v-model="productData.content" editorStyle="height: 400px" />
                            </div>

                        </div>
                    </TabPanel>
                </TabView>


            </div>

            <!-- Cột phải -->
            <div class="col-span-12 lg:col-span-4 ">
                <div class="space-y-6 pb-4 card">
                    <!-- Action Buttons -->
                    <div class="flex justify-between items-center ">
                        <div class="space-x-3">
                            <Link :href="route('admin.products')">
                            <Button label="Quay lại" severity="secondary" />
                            </Link>
                            <Button label="Cập nhật" @click="submitProduct" :loading="submitting" />
                        </div>
                    </div>
                    <h3 class="block text-xl m-0">Thông tin Media</h3>
                    <div class="mt-0">
                        <label class="block mb-2">Ảnh đại diện *</label>
                        <div class="space-y-3">
                            <InputText @change="handleFileChange" type="file" class="w-full" />
                        </div>
                    </div>
                    <div class="mt-0">
                        <label class="block text-xl mb-3">Thư viện ảnh</label>
                        <FileUploads name="images[]" url="/api/admin/products/upload" :multiple="false" accept="image/*"
                            @uploaded="handleUploaded" :images="product.images || []" />
                    </div>
                </div>
                <div class="card">
                    <h3 class="block text-xl mb-3">Thông tin SEO</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block mb-2">Tiêu đề SEO</label>
                            <InputText v-model="seo.meta_title" placeholder="Tiêu đề SEO" class="w-full" />
                        </div>
                        <div>
                            <label class="block mb-2">Mô tả SEO</label>
                            <Textarea v-model="seo.meta_description" rows="3" placeholder="Mô tả SEO" class="w-full" />
                        </div>
                        <div>
                            <label class="block mb-2">Từ khóa SEO</label>
                            <InputText v-model="seo.meta_keywords" placeholder="Từ khóa (cách nhau bởi dấu phẩy)"
                                class="w-full" />
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </AppLayout>
</template>
<style scoped></style>