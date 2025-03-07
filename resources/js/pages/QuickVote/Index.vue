<template>
  <AppLayout title="Quick Vote">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Quick Vote
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Create New Vote -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
          <h3 class="text-lg font-medium mb-4">Create New Vote</h3>
          <form @submit.prevent="createVote" class="space-y-4">
            <div>
              <label for="question" class="block text-sm font-medium text-gray-700">Question</label>
              <input
                type="text"
                id="question"
                v-model="form.question"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                placeholder="Enter your question here..."
              />
            </div>
            <div>
              <button
                type="submit"
                class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                :disabled="form.processing"
              >
                Create Vote
              </button>
            </div>
          </form>
        </div>

        <!-- Active Vote Display -->
        <div v-if="activeVote" class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
          <div class="flex justify-between items-start">
            <div>
              <h3 class="text-lg font-medium mb-2">Active Vote</h3>
              <p class="text-gray-600 mb-4">{{ activeVote.question }}</p>
            </div>
            <button
              @click="toggleVoteStatus(activeVote.id)"
              class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700"
            >
              Close Vote
            </button>
          </div>
          
          <div class="grid md:grid-cols-2 gap-6">
            <!-- QR Code -->
            <div class="flex flex-col items-center justify-center p-4 border rounded-lg">
              <div v-if="qrCode" class="mb-4">
                <img :src="`data:image/svg+xml;base64,${qrCode}`" alt="QR Code" class="w-48 h-48" />
              </div>
              <div v-else class="mb-4 w-48 h-48 flex items-center justify-center bg-gray-100 rounded">
                <p class="text-gray-500 text-sm text-center">QR Code loading...</p>
              </div>
              <p class="text-sm text-gray-500">Scan to vote</p>
              <p class="text-sm font-mono mt-2 break-all">{{ voteUrl }}</p>
            </div>

            <!-- Results -->
            <div class="p-4 border rounded-lg">
              <h4 class="font-medium mb-3">Results</h4>
              <div class="space-y-2">
                <div class="flex justify-between">
                  <span>Yea:</span>
                  <span>{{ activeVote.results.yea }}</span>
                </div>
                <div class="flex justify-between">
                  <span>Nay:</span>
                  <span>{{ activeVote.results.nay }}</span>
                </div>
                <div class="flex justify-between">
                  <span>Abstain:</span>
                  <span>{{ activeVote.results.abstain }}</span>
                </div>
                <div class="flex justify-between pt-2 border-t">
                  <span class="font-medium">Total:</span>
                  <span>{{ activeVote.results.total }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Past Votes -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <h3 class="text-lg font-medium mb-4">Past Votes</h3>
          <div class="space-y-4">
            <div v-for="vote in pastVotes" :key="vote.id" class="border-b pb-4">
              <div class="flex justify-between items-start">
                <div>
                  <h4 class="font-medium">{{ vote.question }}</h4>
                  <p class="text-sm text-gray-500">{{ formatDate(vote.created_at) }}</p>
                </div>
                <div class="text-sm">
                  <span class="font-medium">Total Votes:</span> {{ vote.results.total }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { ref, computed, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import axios from 'axios'

const props = defineProps<{
  votes: {
    id: number
    question: string
    is_active: boolean
    created_at: string
    results: {
      total: number
      yea: number
      nay: number
      abstain: number
    }
    access_code: string
  }[]
}>()

const form = useForm({
  question: '',
})

const qrCode = ref('')
const voteUrl = ref('')

const activeVote = computed(() => props.votes.find(v => v.is_active))

// Update QR code and URL when active vote changes
watch(activeVote, async (newVote) => {
  if (newVote) {
    voteUrl.value = `${window.location.origin}/vote/${newVote.access_code}`
    try {
      const response = await axios.get(route('quick-vote.qr-code', { accessCode: newVote.access_code }))
      qrCode.value = response.data.qr_code
    } catch (error) {
      console.error('Error fetching QR code:', error)
    }
  } else {
    qrCode.value = ''
    voteUrl.value = ''
  }
}, { immediate: true })

const pastVotes = computed(() => props.votes.filter(v => !v.is_active))

const createVote = async () => {
  try {
    const response = await axios.post(route('quick-vote.store'), form)
    qrCode.value = response.data.qr_code
    voteUrl.value = response.data.vote_url
    form.reset()
    // Refresh the page to update the votes list
    window.location.reload()
  } catch (error) {
    console.error('Error creating vote:', error)
  }
}

const toggleVoteStatus = async (id: number) => {
  try {
    await axios.post(route('quick-vote.toggle', { id }))
    // Refresh the page to update the votes list
    window.location.reload()
  } catch (error) {
    console.error('Error toggling vote status:', error)
  }
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}
</script> 