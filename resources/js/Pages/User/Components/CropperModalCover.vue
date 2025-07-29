<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { Cropper, RectangleStencil } from 'vue-advanced-cropper'; // Use RectangleStencil for cover photo
import Close from 'vue-material-design-icons/Close.vue';
import Plus from 'vue-material-design-icons/Plus.vue';
import 'vue-advanced-cropper/dist/style.css';

const emit = defineEmits(['showModal']);

let fileInput = ref(null);
let cropper = ref(null);
let uploadedImage = ref(null);
let croppedImageData = {
    file: null,
    imageUrl: null,
    height: null,
    width: null,
    left: null,
    top: null,
};

const getUploadedImage = (e) => {
    const file = e.target.files[0];
    if (!file) return;
    uploadedImage.value = URL.createObjectURL(file);
};

const crop = () => {
    const { coordinates, canvas } = cropper.value.getResult();
    if (!canvas) return;

    croppedImageData.imageUrl = canvas.toDataURL();

    const data = new FormData();
    
    data.append('cover_image', fileInput.value.files[0] || '');
    data.append('height', coordinates.height || '');
    data.append('width', coordinates.width || '');
    data.append('left', coordinates.left || '');
    data.append('top', coordinates.top || '');

    router.post('/user/update-cover-image', data, {
        preserveState: false,
        onSuccess: () => {
            emit('showModal', false);
        },
    });
};
</script>

<template>
  <div class="fixed z-50 inset-0 flex items-center justify-center bg-white bg-opacity-60">
    <div
      class="relative bg-white rounded-lg shadow-2xl max-w-xl w-full"
    >
      <header class="flex items-center justify-between p-4 border-b border-gray-300">
        <h2 class="text-xl font-extrabold w-full text-center">Update Cover Photo</h2>
        <button
          @click="$emit('showModal', false)"
          class="absolute right-3 top-3 rounded-full p-1.5 bg-gray-200 hover:bg-gray-300 cursor-pointer"
        >
          <Close size="28" fillColor="#5E6771" />
        </button>
      </header>

      <section class="p-4">
        <label
          for="image"
          class="flex items-center justify-center bg-[#E7F3FF] hover:bg-[#DBE7F2] font-bold p-2 rounded-lg text-[#1977F2] w-full cursor-pointer mb-4"
        >
          <Plus size="20" /> Upload photo
        </label>
        <input
          type="file"
          id="image"
          ref="fileInput"
          class="hidden"
          accept="image/*"
          @change="getUploadedImage"
        />

        <div class="w-full max-w-[350px] mx-auto" v-if="uploadedImage">
          <Cropper
            ref="cropper"
            :src="uploadedImage"
            :stencil-component="RectangleStencil"
            :stencil-props="{ aspectRatio: 16 / 9 }"
            class="object-cover w-full h-[200px]"
          />
        </div>

        <div class="flex gap-4 mt-4">
          <button
            @click="$emit('showModal', false)"
            type="button"
            class="flex-1 rounded-md py-2 text-gray-600 hover:text-gray-800 font-bold hover:shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-0"
          >
            Cancel
          </button>
          <button
            v-if="uploadedImage"
            @click="crop"
            type="button"
            class="flex-1 rounded-md bg-blue-500 py-2 text-white font-bold shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-0"
          >
            Crop Image
          </button>
        </div>
      </section>
    </div>
  </div>
</template>
