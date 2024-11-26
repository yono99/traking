<template>
  <div>
    <h1>Buat Team Baru</h1>
    <form @submit.prevent="submit">
      <div>
        <label>Nama Team:</label>
        <input v-model="form.name" type="text" required />
        <span v-if="errors.name">{{ errors.name }}</span>
      </div>
      <button type="submit">Buat</button>
    </form>
  </div>
</template>

<script>
import { useForm } from '@inertiajs/inertia-vue3'

export default {
  setup() {
    const form = useForm({
      name: '',
    })

    const submit = () => {
      form.post('/teams')
    }

    return { form, submit }
  },
}
</script>
