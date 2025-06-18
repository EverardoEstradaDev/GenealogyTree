<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';

const isDragging = ref(false);
const uploadProgress = ref(0);
const isUploading = ref(false);
const uploadedFiles = ref([]);
const dropZoneRef = ref(null);

const handleDragEnter = (e) => {
  e.preventDefault();
  e.stopPropagation();
  isDragging.value = true;
};

const handleDragLeave = (e) => {
  e.preventDefault();
  e.stopPropagation();
  isDragging.value = false;
};

const handleDragOver = (e) => {
  e.preventDefault();
  e.stopPropagation();
  isDragging.value = true;
};

const handleDrop = (e) => {
  e.preventDefault();
  e.stopPropagation();
  isDragging.value = false;
  
  const files = e.dataTransfer.files;
  if (files.length) {
    handleFiles(files);
  }
};

const handleFileInput = (e) => {
  const files = e.target.files;
  if (files.length) {
    handleFiles(files);
  }
};

const handleFiles = (files) => {
  isUploading.value = true;
  uploadProgress.value = 0;
  
  // Store the actual File objects for later use
  const fileObjects = Array.from(files);
  
  // Simulate progress for demo purposes
  const interval = setInterval(() => {
    uploadProgress.value += 100;
    if (uploadProgress.value >= 100) {
      clearInterval(interval);
      isUploading.value = false;
      
      // Add files to the uploaded files list
      fileObjects.forEach((file, index) => {
        uploadedFiles.value.push({
          name: file.name,
          size: file.size,
          type: file.type,
          lastModified: file.lastModified,  
          id: Date.now() + Math.random().toString(36).substring(2, 9), // Generate a unique ID
          fileObject: file, // Store the actual File object
          index: index // Store the index for reference
        });
      });
    }
  }, 200);
};

const openFile = (fileId) => {
  // Find the file with the given ID
  const fileToOpen = uploadedFiles.value.find(file => file.id === fileId);
  
  if (fileToOpen && fileToOpen.fileObject) {
    // Create FormData to send the file
    const formData = new FormData();
    formData.append('document', fileToOpen.fileObject);
    
    // Send the file to the server using Inertia router
    router.post('/uploadfile', formData);
  } else {
    console.error('File not found or file object is missing');
  }
};

// Add global event listeners for drag and drop
const addGlobalListeners = () => {
  window.addEventListener('dragenter', handleDragEnter);
  window.addEventListener('dragleave', handleDragLeave);
  window.addEventListener('dragover', handleDragOver);
  window.addEventListener('drop', handleDrop);
};

const removeGlobalListeners = () => {
  window.removeEventListener('dragenter', handleDragEnter);
  window.removeEventListener('dragleave', handleDragLeave);
  window.removeEventListener('dragover', handleDragOver);
  window.removeEventListener('drop', handleDrop);
};

onMounted(() => {
  addGlobalListeners();
});

onUnmounted(() => {
  removeGlobalListeners();
});
</script>

<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="flex min-h-screen flex-col items-center bg-[#FDFDFC] p-6 text-[#1b1b18] lg:justify-center lg:p-8 dark:bg-[#0a0a0a]">
        <header class="mb-6 w-full max-w-[335px] text-sm not-has-[nav]:hidden lg:max-w-4xl">
            <nav class="flex items-center justify-end gap-4">
                <Link
                    v-if="$page.props.auth.user"
                    :href="route('dashboard')"
                    class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                >
                    Dashboard
                </Link>
                <template v-else>
                    <Link
                        :href="route('login')"
                        class="inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#19140035] dark:text-[#EDEDEC] dark:hover:border-[#3E3E3A]"
                    >
                        Log in
                    </Link>
                    <Link
                        :href="route('register')"
                        class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                    >
                        Register
                    </Link>
                </template>
            </nav>
        </header>
        <div class="flex w-full items-center justify-center opacity-100 transition-opacity duration-750 lg:grow starting:opacity-0">
            <main class="flex w-full max-w-[335px] flex-col-reverse overflow-hidden rounded-lg lg:max-w-4xl lg:flex-row">
                <div
                    class="flex-1 rounded-br-lg rounded-bl-lg bg-white p-6 pb-12 text-[13px] leading-[20px] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] lg:rounded-tl-lg lg:rounded-br-none lg:p-20 dark:bg-[#161615] dark:text-[#EDEDEC] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]"
                >
                    <h1 class="mb-1 font-medium">Welcome!</h1>
                    <p class="mb-2 text-[#706f6c] dark:text-[#A1A09A]">
                        To begin, deposit the visualization file you wish to open
                    </p>
                    
                    <!-- Document Dropper -->
                    <div 
                        ref="dropZoneRef"
                        :class="[
                            'mt-6 mb-6 p-6 border-2 border-dashed rounded-lg text-center transition-all',
                            isDragging ? 'border-[#f53003] bg-[#fff2f2] dark:bg-[#1D0002]' : 'border-[#e3e3e0] dark:border-[#3E3E3A]'
                        ]"
                        @dragenter.prevent="isDragging = true"
                        @dragleave.prevent="isDragging = false"
                        @dragover.prevent="isDragging = true"
                        @drop.prevent="handleDrop"
                    >
                        <div v-if="isUploading" class="mb-4">
                            <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                <div class="bg-[#f53003] h-2.5 rounded-full dark:bg-[#FF4433]" :style="{ width: uploadProgress + '%' }"></div>
                            </div>
                            <p class="mt-2 text-sm">Uploading... {{ uploadProgress }}%</p>
                        </div>
                        <div v-else>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-[#706f6c] dark:text-[#A1A09A]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <p class="mt-2">Drag and drop documents here or</p>
                            <label class="mt-2 inline-block cursor-pointer">
                                <span class="text-[#f53003] underline underline-offset-4 dark:text-[#FF4433]">browse files</span>
                                <input type="file" class="hidden" @change="handleFileInput" multiple />
                            </label>
                        </div>
                    </div>
                    
                    <!-- Uploaded Files List -->
                    <div v-if="uploadedFiles.length > 0" class="mt-4 mb-6">
                        <h2 class="mb-2 font-medium">Uploaded Documents</h2>
                        <ul class="space-y-2">
                            <li v-for="(file, index) in uploadedFiles" :key="index" class="flex items-center justify-between p-2 bg-[#FDFDFC] rounded border border-[#e3e3e0] dark:bg-[#1D1D1B] dark:border-[#3E3E3A]">
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-[#706f6c] dark:text-[#A1A09A]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span class="truncate max-w-[150px] lg:max-w-[250px]">{{ file.name }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">
                                        {{ Math.round(file.size / 1024) }} KB
                                    </span>
                                    <button 
                                        @click="openFile(file.id)" 
                                        class="px-3 py-1 text-xs rounded-sm bg-[#f53003] text-white hover:bg-[#d52a00] transition-colors dark:bg-[#FF4433] dark:hover:bg-[#e53b2b]"
                                    >
                                        Abrir
                                    </button>
                                </div>
                            </li>
                        </ul>
                    </div>
                 
                </div>
                         </main>
        </div>
        <div class="hidden h-14.5 lg:block"></div>
    </div>
</template>
