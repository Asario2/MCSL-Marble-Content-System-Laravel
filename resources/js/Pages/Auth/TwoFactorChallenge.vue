<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100 dark:bg-gray-900">
      <div class="w-full max-w-md p-8 bg-white dark:bg-gray-800 rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-gray-100 text-center">
          Zwei-Faktor-Authentifizierung
        </h2>

        <form @submit.prevent="submit" class="space-y-4">
          <div>
            <label for="code" class="block text-gray-700 dark:text-gray-300 mb-1">OTP-Code</label>
            <input
              type="text"
              id="code"
              v-model="form.code"
              placeholder="123456"
              class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100"
            />
            <p v-if="errors.code" class="text-red-500 text-sm mt-1">{{ errors.code }}</p>
          </div>

          <div>
            <label for="recovery_code" class="block text-gray-700 dark:text-gray-300 mb-1">Recovery-Code (optional)</label>
            <input
              type="text"
              id="recovery_code"
              v-model="form.recovery_code"
              placeholder="abcd-efgh-ijkl"
              class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100"
            />
            <p v-if="errors.recovery_code" class="text-red-500 text-sm mt-1">{{ errors.recovery_code }}</p>
          </div>

          <button
            type="submit"
            class="w-full py-2 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg focus:outline-none focus:ring focus:ring-indigo-300"
          >
            Einloggen
          </button>

          <p v-if="errors.general" class="text-red-500 text-sm mt-2 text-center">{{ errors.general }}</p>
        </form>
      </div>
    </div>
  </template>

  <script>
  import { ref } from 'vue';
  import { Inertia } from '@inertiajs/inertia';

  export default {
    setup() {
      const form = ref({
        code: '',
        recovery_code: '',
        two_factor_enabled:'1',
      });

      const errors = ref({});

      const submit = () => {
        errors.value = {};
        Inertia.post('/two-factor-login', form.value, {
          onError: (err) => {
            errors.value = err;
          },
        });
      };

      return { form, errors, submit };
    },
  };
  </script>
