<script setup>
import { ref } from "vue";
import { router } from '@inertiajs/vue3'
import { usePrimeVue } from "primevue/config";
import useNotify from "@/Composables/useNotify";
import ShowImage from "../client/Product/ShowImage.vue";
const props = defineProps({
    name: { type: String, default: "images[]" },
    url: { type: String, default: "" },
    multiple: { type: Boolean, default: true },
    accept: { type: String, default: "image/*" },
    maxFileSize: { type: Number, default: 2000000 },
    images: { type: Array, default: () => [] }
});

const notify = useNotify();
const emit = defineEmits(["uploaded", "selected"]);

const totalSize = ref(0);
const totalSizePercent = ref(0);
const $primevue = usePrimeVue();

// --- Selected
const onSelectedFiles = (event) => {
    totalSize.value = event.files.reduce((acc, f) => acc + f.size, 0);
    emit("selected", event.files);
};

// --- Remove
const removeTemplatingFile = (file, removeFileCallback, index) => {
    removeFileCallback(index);
    totalSize.value -= file.size;
    totalSizePercent.value = (totalSize.value / props.maxFileSize) * 100;
};

// --- Clear
const clearTemplatingUpload = (clear) => {
    clear();
    totalSize.value = 0;
    totalSizePercent.value = 0;
};

// --- Upload trigger
const uploadEvent = (callback) => {
    totalSizePercent.value = (totalSize.value / props.maxFileSize) * 100;
    callback();
};

// --- Format size
const formatSize = (bytes) => {
    if (bytes === 0) return "0 B";
    const k = 1024;
    const sizes = $primevue.config.locale.fileSizeTypes;
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return `${(bytes / Math.pow(k, i)).toFixed(2)} ${sizes[i]}`;
};

// --- Upload success
const onUpload = (event) => {
    try {
        const res = JSON.parse(event.xhr.response);
        if (res.files?.length) {
            emit("uploaded", res.files);
        }
    } catch (e) {
        notify.error("Có lỗi xảy ra khi xử lý dữ liệu server");
    }
};

// --- Upload error
const onUploadError = (event) => {
    try {
        const res = JSON.parse(event.xhr.response);
        notify.error(res.errors || "Có lỗi xảy ra khi tải ảnh");
    } catch (e) {
        notify.error("Upload thất bại, vui lòng thử lại");
    }
};
const deleteImage = (id) => {
    router.delete(route('admin.image.delete', { id: id }), {
        preserveScroll: true,
        onSuccess: () => {
            notify.success('Xóa ảnh thành công!');
        },
        onError: (errors) => {
            Object.values(errors).forEach(errorArray => {
                notify.error(errorArray)
            });
        }
    })
}

</script>
<template>
    <FileUpload :name="name" :url="url" :multiple="multiple" :accept="accept" :maxFileSize="maxFileSize"
        @upload="onUpload" @select="onSelectedFiles" @error="onUploadError" chooseLabel="Chọn ảnh">
        <!-- HEADER -->
        <template #header="{ chooseCallback, uploadCallback, clearCallback, files }">
            <div class="flex flex-wrap justify-between items-center flex-1 gap-4">
                <div class="flex gap-2">
                    <Button v-tooltip.top="'Chọn ảnh'" @click="chooseCallback()" icon="pi pi-images" rounded
                        variant="outlined" severity="secondary" />
                    <Button v-tooltip.top="'Tải lên'" @click="uploadEvent(uploadCallback)" icon="pi pi-cloud-upload"
                        rounded variant="outlined" severity="success" :disabled="!files?.length" />
                    <Button v-tooltip.top="'Xóa tất cả'" @click="clearTemplatingUpload(clearCallback)"
                        icon="pi pi-times" rounded variant="outlined" severity="danger" :disabled="!files?.length" />
                </div>
                <span class="text-sm text-muted-color">
                    (<i>tải 1 loạt ảnh luôn không tải từng ảnh một!</i>)
                </span>
                <ProgressBar :value="totalSizePercent" :showValue="false" class="md:w-20rem h-1 w-full md:ml-auto">
                    <span class="whitespace-nowrap">{{ totalSize }} / {{ formatSize(maxFileSize) }}</span>
                </ProgressBar>
            </div>
        </template>

        <!-- CONTENT -->
        <template #content="{ files, uploadedFiles, removeUploadedFileCallback, removeFileCallback }">
            <div class="flex flex-col gap-8 ">
                <!-- Pending -->
                <div v-if="files?.length">
                    <h5 class="pending-upload-title text-lg font-bold text-primary mb-2">Chờ tải lên</h5>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 w-full">
                        <div v-for="(file, index) in files" :key="file.name + index"
                            class="p-2 border rounded flex flex-col items-center gap-4 relative">
                            <ShowImage preview :alt="file.name" :src="file.objectURL" class="w-full show-img img-fluid" />
                            <Button icon="pi pi-times" @click="removeTemplatingFile(file, removeFileCallback, index)"
                                variant="outlined" rounded severity="danger" />
                        </div>
                    </div>
                </div>
                <!-- Uploaded -->
                <div v-if="uploadedFiles?.length">
                    <h5 class="pending-upload-title text-lg font-bold text-primary mb-2">Đã tải lên</h5>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 w-full">
                        <div v-for="(file, index) in uploadedFiles" :key="file.name + index"
                            class="p-2 border rounded flex flex-col items-center gap-4 relative">
                            <ShowImage preview :alt="file.name" :src="file.objectURL" class="w-full show-img img-fluid" />
                            <Button icon="pi pi-times"
                                @click="removeTemplatingFile(file, removeUploadedFileCallback, index)"
                                variant="outlined" rounded severity="danger" />
                        </div>

                    </div>
                </div>
                <div v-if="images?.length">
                    <h5 class="pending-upload-title text-lg font-bold text-primary mb-2">Đã tải lên server</h5>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 w-full">
                        <div v-for="(file, index) in images" :key="file.id"
                            class="p-2 border rounded flex flex-col items-center gap-4 relative">
                            <ShowImage preview :alt="file?.name" :src="file?.image_path || file.url"
                                class="w-full show-img img-fluid" />
                            <Button icon="pi pi-times" @click="deleteImage(file.id)" variant="outlined" rounded
                                severity="danger" />
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- EMPTY -->
        <template #empty v-if="!images?.length">
            <div class="flex items-center justify-center flex-col">
                <i class="pi pi-cloud-upload border-2 rounded-full p-8 text-4xl text-muted-color" />
                <p class="mt-6 mb-0">Kéo thả hình ảnh vào đây hoặc click để chọn.</p>
            </div>
        </template>
    </FileUpload>
</template>
<style scoped>
.show-img {
    width: 100% !important;
    height: 100px !important;
    object-fit: cover !important;
}
</style>