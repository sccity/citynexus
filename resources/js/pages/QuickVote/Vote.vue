<template>
  <div class="min-h-screen bg-gray-100 py-6 flex flex-col justify-center sm:py-12">
    <div class="relative py-3 sm:max-w-xl sm:mx-auto">
      <div class="absolute inset-0 bg-gradient-to-r from-cyan-400 to-light-blue-500 shadow-lg transform -skew-y-6 sm:skew-y-0 sm:-rotate-6 sm:rounded-3xl"></div>
      <div class="relative px-4 py-10 bg-white shadow-lg sm:rounded-3xl sm:p-20">
        <div class="max-w-md mx-auto">
          <div class="divide-y divide-gray-200">
            <div class="py-8 text-base leading-6 space-y-4 text-gray-700 sm:text-lg sm:leading-7">
              <h2 class="text-2xl font-bold mb-8 text-center">{{ vote.question }}</h2>
              
              <form @submit.prevent="submitVote" class="space-y-6" v-if="!submitted">
                <div>
                  <label for="name" class="block text-sm font-medium text-gray-700">Your Name</label>
                  <input
                    type="text"
                    id="name"
                    v-model="form.voter_name"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    required
                  />
                </div>

                <div class="space-y-4">
                  <label class="block text-sm font-medium text-gray-700">Your Vote</label>
                  <div class="space-y-2">
                    <button
                      type="button"
                      @click="form.response = 'yea'"
                      :class="[
                        'w-full px-4 py-2 text-sm font-medium rounded-md',
                        form.response === 'yea'
                          ? 'bg-green-600 text-white'
                          : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'
                      ]"
                    >
                      Yea
                    </button>
                    <button
                      type="button"
                      @click="form.response = 'nay'"
                      :class="[
                        'w-full px-4 py-2 text-sm font-medium rounded-md',
                        form.response === 'nay'
                          ? 'bg-red-600 text-white'
                          : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'
                      ]"
                    >
                      Nay
                    </button>
                    <button
                      type="button"
                      @click="form.response = 'abstain'"
                      :class="[
                        'w-full px-4 py-2 text-sm font-medium rounded-md',
                        form.response === 'abstain'
                          ? 'bg-gray-600 text-white'
                          : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'
                      ]"
                    >
                      Abstain
                    </button>
                  </div>
                </div>

                <div>
                  <button
                    type="submit"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    :disabled="!form.voter_name || !form.response"
                  >
                    Submit Vote
                  </button>
                </div>
              </form>

              <div v-else class="text-center">
                <div class="rounded-full bg-green-100 p-3 mx-auto w-16 h-16 flex items-center justify-center mb-4">
                  <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900">Vote Submitted!</h3>
                <p class="mt-2 text-sm text-gray-500">Thank you for participating.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import axios from 'axios'

const props = defineProps<{
  vote: {
    id: number
    question: string
    access_code: string
  }
}>()

const form = ref({
  voter_name: '',
  response: '',
})

const submitted = ref(false)

const submitVote = async () => {
  try {
    await axios.post(`/vote/${props.vote.access_code}`, form.value)
    submitted.value = true
  } catch (error) {
    console.error('Error submitting vote:', error)
  }
}
</script> 