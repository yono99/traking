<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import UserStatus from '@/Pages/Profile/Partials/UserStatus.vue'
import LogoutOtherBrowserSessionsForm from '@/Pages/Profile/Partials/LogoutOtherBrowserSessionsForm.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import TwoFactorAuthenticationForm from '@/Pages/Profile/Partials/TwoFactorAuthenticationForm.vue';
import UpdatePasswordForm from '@/Pages/Profile/Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from '@/Pages/Profile/Partials/UpdateProfileInformationForm.vue';

defineProps({
    confirmsTwoFactorAuthentication: Boolean,
    sessions: Array,
});
</script>

<template>
    <AppLayout title="Profile">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Profile
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <!-- Status User Component -->
                <UserStatus />
                <SectionBorder />

                <!-- Profile Information -->
                <div v-if="$page.props.jetstream.canUpdateProfileInformation">
                    <UpdateProfileInformationForm 
                        :user="$page.props.auth.user"
                        class="mt-10 sm:mt-0" 
                    />
                    <SectionBorder />
                </div>

                <!-- Update Password -->
                <div v-if="$page.props.jetstream.canUpdatePassword">
                    <UpdatePasswordForm class="mt-10 sm:mt-0" />
                    <SectionBorder />
                </div>

                <!-- Two Factor Authentication -->
                <div v-if="$page.props.jetstream.canManageTwoFactorAuthentication">
                    <TwoFactorAuthenticationForm
                        :requires-confirmation="confirmsTwoFactorAuthentication"
                        class="mt-10 sm:mt-0"
                    />
                    <SectionBorder />
                </div>

                <!-- Browser Sessions -->
                <LogoutOtherBrowserSessionsForm 
                    :sessions="sessions" 
                    class="mt-10 sm:mt-0" 
                />
            </div>
        </div>
    </AppLayout>
</template>