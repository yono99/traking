<!-- resources/js/Components/UpdateServiceModal.vue -->
<template>
    <Modal :show="show" @close="closeModal">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                Update Service Data
            </h2>

            <form @submit.prevent="submitForm" class="mt-6 space-y-6">
                <!-- Service Fields -->
                <div>
                    <InputLabel for="name" value="Name" />
                    <TextInput
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="mt-1 block w-full"
                    />
                    <InputError :message="form.errors.name" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="status" value="Status" />
                    <select
                        id="status"
                        v-model="form.status"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                    >
                        <option value="PROSES VERIFIKASI">PROSES VERIFIKASI</option>
                        <option value="PROSES VERIFIKASI LANJUTAN">PROSES VERIFIKASI LANJUTAN</option>
                        <option value="PROSES VERIFIKASI CROSSCHECK">PROSES VERIFIKASI CROSSCHECK</option>
                        <!-- Tambahkan status lainnya sesuai kebutuhan -->
                    </select>
                    <InputError :message="form.errors.status" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="remarks" value="Remarks" />
                    <textarea
                        id="remarks"
                        v-model="form.remarks"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                        rows="3"
                    ></textarea>
                    <InputError :message="form.errors.remarks" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="PNBP" value="PNBP" />
                    <TextInput
                        id="PNBP"
                        v-model="form.PNBP"
                        type="text"
                        class="mt-1 block w-full"
                    />
                    <InputError :message="form.errors.PNBP" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="nomor_hp" value="Nomor HP" />
                    <TextInput
                        id="nomor_hp"
                        v-model="form.nomor_hp"
                        type="text"
                        class="mt-1 block w-full"
                    />
                    <InputError :message="form.errors.nomor_hp" class="mt-2" />
                </div>

                <!-- Land Book Fields -->
                <div>
                    <InputLabel for="nomer_hak" value="Nomer Hak" />
                    <TextInput
                        id="nomer_hak"
                        v-model="form.land_book.nomer_hak"
                        type="text"
                        class="mt-1 block w-full"
                    />
                    <InputError :message="form.errors.nomer_hak" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="jenis_hak" value="Jenis Hak" />
                    <TextInput
                        id="jenis_hak"
                        v-model="form.land_book.jenis_hak"
                        type="text"
                        class="mt-1 block w-full"
                    />
                    <InputError :message="form.errors.jenis_hak" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="desa_kecamatan" value="Desa - Kecamatan" />
                    <TextInput
                        id="desa_kecamatan"
                        v-model="form.land_book.desa_kecamatan"
                        type="text"
                        class="mt-1 block w-full"
                    />
                    <InputError :message="form.errors.desa_kecamatan" class="mt-2" />
                </div>

                <div class="flex items-center gap-4">
                    <PrimaryButton :disabled="form.processing">Save</PrimaryButton>
                    <SecondaryButton @click="closeModal">Cancel</SecondaryButton>
                </div>
            </form>
        </div>
    </Modal>
</template>

<script>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import Modal from '@/Components/Modal.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import InputError from '@/Components/InputError.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'

export default {
    components: {
        Modal,
        InputLabel,
        TextInput,
        InputError,
        PrimaryButton,
        SecondaryButton,
    },

    props: {
        show: Boolean,
        service: Object,
    },

    emits: ['close'],

    setup(props, { emit }) {
        const form = useForm({
            name: props.service?.name || '',
            status: props.service?.status || '',
            remarks: props.service?.remarks || '',
            PNBP: props.service?.PNBP || '',
            nomor_hp: props.service?.nomor_hp || '',
            land_book: {
                nomer_hak: props.service?.land_book?.nomer_hak || '',
                jenis_hak: props.service?.land_book?.jenis_hak || '',
                desa_kecamatan: props.service?.land_book?.desa_kecamatan || '',
                status_alih_media: props.service?.land_book?.status_alih_media || '',
            }
        })

        const submitForm = () => {
            form.put(route('services.update', props.service.id), {
                onSuccess: () => {
                    emit('close')
                    form.reset()
                },
            })
        }

        const closeModal = () => {
            emit('close')
            form.reset()
        }

        return {
            form,
            submitForm,
            closeModal,
        }
    },
}
</script>