<script setup>
import { ref, computed, onMounted } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import axios from "axios";

const props = defineProps({
    sessions: { type: Array, default: () => [] },
});

const sessions      = ref([...props.sessions]);
const loading       = ref(false);
const loadingStart  = ref(false);
const loadingAction = ref(null);
const confirmTarget = ref(null);
const confirmChange = ref(false);
const showQr        = ref({});
const qrLoading     = ref({});
const qrBase64      = ref({});
const qrError       = ref({});
const pollIntervals = ref({});
const toast         = ref({ show: false, message: "", type: "success" });

const activeSession = computed(() => sessions.value.find((s) => s.is_active) ?? null);

const showToast = (message, type = "success") => {
    toast.value = { show: true, message, type };
    setTimeout(() => (toast.value.show = false), 4000);
};

const formatDate = (d) => {
    if (!d) return "-";
    return new Date(d).toLocaleDateString("id-ID", {
        day: "numeric", month: "short", year: "numeric"
    });
};

const fetchSessions = async () => {
    loading.value = true;
    try {
        const res = await axios.get("/wa/sessions");
        sessions.value = res.data ?? [];
    } catch (err) {
        showToast(err.response?.data?.message ?? "Gagal memuat session", "error");
    } finally {
        loading.value = false;
    }
};

const startNewSession = async () => {
    loadingStart.value = true;
    try {
        const res = await axios.post("/wa/sessions/start");
        showToast(`Session "${res.data.session_name}" dibuat. Klik Tampilkan QR untuk scan.`);
        await fetchSessions();
    } catch (err) {
        showToast(err.response?.data?.message ?? "Gagal membuat session", "error");
    } finally {
        loadingStart.value = false;
    }
};

const stopPoll = (id) => {
    if (pollIntervals.value[id]) {
        clearInterval(pollIntervals.value[id]);
        delete pollIntervals.value[id];
    }
};

const pollStatus = (session) => {
    stopPoll(session.id);
    let attempts = 0;
    pollIntervals.value[session.id] = setInterval(async () => {
        attempts++;
        if (attempts > 40) { stopPoll(session.id); return; }
        try {
            const res = await axios.get(`/wa/sessions/status/${session.session_name}`);
            if (res.data.status === "connected") {
                stopPoll(session.id);
                showToast(`✅ WhatsApp "${session.session_name}" berhasil terhubung!`);
                showQr.value[session.id] = false;
                await fetchSessions();
            }
        } catch {
            stopPoll(session.id);
        }
    }, 2000);
};

const loadQr = async (session) => {
    qrLoading.value[session.id] = true;
    qrBase64.value[session.id]  = "";
    qrError.value[session.id]   = false;
    try {
        const res = await axios.get(`/wa/sessions/qr/${session.session_name}`);
        qrBase64.value[session.id] = res.data.qr;
        pollStatus(session); // ← tambah ini
    } catch (err) {
        qrError.value[session.id] = true;
        showToast("Gagal memuat QR", "error");
    } finally {
        qrLoading.value[session.id] = false;
    }
};

const toggleQr = (session) => {
    if (showQr.value[session.id]) {
        showQr.value[session.id] = false;
        stopPoll(session.id);
        return;
    }
    showQr.value[session.id] = true;
    loadQr(session);
};

const doChangeSession = async () => {
    confirmChange.value = false;
    loadingStart.value  = true;
    try {
        if (activeSession.value) {
            stopPoll(activeSession.value.id);
            await axios.delete(`/wa/sessions/${activeSession.value.id}`);
        }
        const res = await axios.post("/wa/sessions/start");
        showToast(`Session baru "${res.data.session_name}" dibuat. Silakan scan QR.`);
        await fetchSessions();
    } catch (err) {
        showToast(err.response?.data?.message ?? "Gagal ganti session", "error");
    } finally {
        loadingStart.value = false;
    }
};

const confirmLogout = (session) => { confirmTarget.value = session; };

const doLogout = async () => {
    if (!confirmTarget.value) return;
    const target = confirmTarget.value;
    confirmTarget.value = null;
    loadingAction.value = target.id;
    try {
        stopPoll(target.id);
        await axios.delete(`/wa/sessions/${target.id}`);
        showToast("Session berhasil dihapus");
        await fetchSessions();
    } catch (err) {
        showToast(err.response?.data?.message ?? "Gagal logout session", "error");
    } finally {
        loadingAction.value = null;
    }
};

onMounted(fetchSessions);
</script>

<template>
    <AppLayout title="WA Gateway">
        <template #header>
            <div class="flex items-center gap-3">
                <div class="wag-header-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                </div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    WhatsApp Gateway
                </h2>
            </div>
        </template>

        <div class="wag-page">

            <!-- ── Top Bar ─────────────────────────────────────── -->
            <div class="wag-topbar">
                <!-- Status pill -->
                <div class="wag-status-pill" :class="activeSession ? 'wag-pill-on' : 'wag-pill-off'">
                    <span class="wag-pill-dot"></span>
                    <span v-if="activeSession">
                        Terhubung · <strong>{{ activeSession.session_name }}</strong>
                    </span>
                    <span v-else>Tidak ada session aktif</span>
                </div>

                <!-- Actions -->
                <div class="wag-topbar-actions">
                    <button
                        v-if="!activeSession"
                        class="wag-btn wag-btn-primary"
                        @click="startNewSession"
                        :disabled="loadingStart"
                    >
                        <span v-if="loadingStart" class="wag-spin-sm"></span>
                        <svg v-else width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                        Session Baru
                    </button>
                    <button
                        v-else
                        class="wag-btn wag-btn-ghost"
                        @click="confirmChange = true"
                        :disabled="loadingStart"
                    >
                        <span v-if="loadingStart" class="wag-spin-sm dark"></span>
                        <svg v-else width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Ganti Session
                    </button>
                </div>
            </div>

            <!-- ── Loading ─────────────────────────────────────── -->
            <div v-if="loading" class="wag-center-state">
                <div class="wag-ring"></div>
                <p class="wag-state-text">Memuat session...</p>
            </div>

            <!-- ── Empty ──────────────────────────────────────── -->
            <div v-else-if="sessions.length === 0" class="wag-center-state">
                <div class="wag-empty-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z"/>
                    </svg>
                </div>
                <p class="wag-state-title">Belum ada session</p>
                <p class="wag-state-text">Klik <strong>Session Baru</strong> untuk menghubungkan WhatsApp</p>
            </div>

            <!-- ── Session Cards ──────────────────────────────── -->
            <div v-else class="wag-grid">
                <div
                    v-for="session in sessions"
                    :key="session.id"
                    class="wag-card"
                    :class="{ 'wag-card-active': session.is_active }"
                >
                    <!-- Card header -->
                    <div class="wag-card-head">
                        <div class="wag-card-left">
                            <div class="wag-avatar" :class="session.is_active ? 'wag-avatar-on' : 'wag-avatar-off'">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="wag-session-name">{{ session.session_name }}</div>
                                <div class="wag-session-date">Dibuat {{ formatDate(session.created_at) }}</div>
                            </div>
                        </div>
                        <div class="wag-card-right">
                            <span v-if="session.is_active" class="wag-badge-active">
                                <span class="wag-pulse"></span> Aktif
                            </span>
                            <span v-else class="wag-badge-idle">Standby</span>
                        </div>
                    </div>

                    <!-- Card actions -->
                    <div class="wag-card-actions">
                        <button
                            class="wag-btn-action wag-btn-qr"
                            :class="{ 'wag-btn-qr-open': showQr[session.id] }"
                            @click="toggleQr(session)"
                        >
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="7" height="7" rx="1"/>
                                <rect x="14" y="3" width="7" height="7" rx="1"/>
                                <rect x="3" y="14" width="7" height="7" rx="1"/>
                                <path d="M14 14h3v3h-3zM17 17h3v3h-3zM14 20h3"/>
                            </svg>
                            {{ showQr[session.id] ? 'Tutup QR' : 'Tampilkan QR' }}
                        </button>
                        <button
                            class="wag-btn-action wag-btn-del"
                            @click="confirmLogout(session)"
                            :disabled="loadingAction === session.id"
                            title="Hapus session"
                        >
                            <span v-if="loadingAction === session.id" class="wag-spin-sm dark"></span>
                            <svg v-else width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </button>
                    </div>

                    <!-- QR Panel -->
                    <transition name="qr-slide">
                        <div v-if="showQr[session.id]" class="wag-qr-panel">
                            <p class="wag-qr-instruction">
                                📱 Buka WhatsApp → <strong>Linked Devices</strong> → <strong>Link a Device</strong> → Scan QR
                            </p>

                            <!-- Loading -->
                            <div v-if="qrLoading[session.id]" class="wag-qr-placeholder">
                                <div class="wag-ring"></div>
                                <p>Mengambil QR dari gateway...</p>
                            </div>

                            <!-- QR Image -->
                            <div v-else-if="qrBase64[session.id]" class="wag-qr-frame">
                                <img
                                    :src="qrBase64[session.id]"
                                    alt="QR WhatsApp"
                                    class="wag-qr-img"
                                />
                                <div class="wag-qr-corner wag-qr-tl"></div>
                                <div class="wag-qr-corner wag-qr-tr"></div>
                                <div class="wag-qr-corner wag-qr-bl"></div>
                                <div class="wag-qr-corner wag-qr-br"></div>
                            </div>

                            <!-- Error -->
                            <div v-else class="wag-qr-placeholder wag-qr-err">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                                </svg>
                                <p>Gagal memuat QR</p>
                            </div>

                            <div class="wag-qr-footer">
                                <p class="wag-qr-note">QR hanya berlaku beberapa menit · status diperbarui otomatis</p>
                                <button
                                    class="wag-btn wag-btn-ghost wag-btn-sm"
                                    @click="loadQr(session)"
                                    :disabled="qrLoading[session.id]"
                                >
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    Refresh QR
                                </button>
                            </div>
                        </div>
                    </transition>
                </div>
            </div>

            <!-- ── Panduan ─────────────────────────────────────── -->
            <div class="wag-guide">
                <div class="wag-guide-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="wag-guide-title">Cara menghubungkan WhatsApp</p>
                    <ol class="wag-guide-list">
                        <li>Klik <strong>Session Baru</strong> — nama session digenerate otomatis</li>
                        <li>Klik <strong>Tampilkan QR</strong> pada session yang muncul</li>
                        <li>Buka WhatsApp di HP → <strong>Linked Devices</strong> → <strong>Link a Device</strong></li>
                        <li>Scan QR — status otomatis berubah jadi <strong>Aktif</strong></li>
                        <li>Untuk ganti nomor, klik <strong>Ganti Session</strong></li>
                    </ol>
                </div>
            </div>

            <!-- ── Modal Logout ───────────────────────────────── -->
            <transition name="modal-fade">
                <div v-if="confirmTarget" class="wag-overlay" @click.self="confirmTarget = null">
                    <div class="wag-modal">
                        <div class="wag-modal-icon wag-modal-danger">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                            </svg>
                        </div>
                        <h3 class="wag-modal-title">Hapus Session?</h3>
                        <p class="wag-modal-desc">
                            Session <strong>{{ confirmTarget?.session_name }}</strong> akan dihapus dan WhatsApp akan terputus.
                        </p>
                        <div class="wag-modal-actions">
                            <button class="wag-btn wag-btn-ghost" @click="confirmTarget = null">Batal</button>
                            <button class="wag-btn wag-btn-danger" @click="doLogout" :disabled="loadingAction !== null">
                                Ya, Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </transition>

            <!-- ── Modal Ganti Session ────────────────────────── -->
            <transition name="modal-fade">
                <div v-if="confirmChange" class="wag-overlay" @click.self="confirmChange = false">
                    <div class="wag-modal">
                        <div class="wag-modal-icon wag-modal-warn">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                        </div>
                        <h3 class="wag-modal-title">Ganti Session WA?</h3>
                        <p class="wag-modal-desc">
                            Session <strong>{{ activeSession?.session_name }}</strong> akan dilogout, lalu session baru dibuat untuk di-scan ulang.
                        </p>
                        <div class="wag-modal-actions">
                            <button class="wag-btn wag-btn-ghost" @click="confirmChange = false">Batal</button>
                            <button class="wag-btn wag-btn-warn" @click="doChangeSession" :disabled="loadingStart">
                                <span v-if="loadingStart" class="wag-spin-sm"></span>
                                <span v-else>Ya, Ganti</span>
                            </button>
                        </div>
                    </div>
                </div>
            </transition>

            <!-- ── Toast ──────────────────────────────────────── -->
            <transition name="toast-slide">
                <div v-if="toast.show" class="wag-toast" :class="`wag-toast-${toast.type}`">
                    <svg v-if="toast.type === 'success'" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ toast.message }}
                </div>
            </transition>

        </div>
    </AppLayout>
</template>

<style scoped>
/* ── Base ───────────────────────────────────────────────────────── */
.wag-page {
    max-width: 860px;
    margin: 0 auto;
    padding: 2rem 1.5rem 4rem;
    font-family: 'Plus Jakarta Sans', 'Segoe UI', sans-serif;
}

/* ── Header icon ─────────────────────────────────────────────────*/
.wag-header-icon {
    width: 32px; height: 32px;
    background: #25d366;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    color: white;
}

/* ── Top bar ─────────────────────────────────────────────────────*/
.wag-topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1.5rem;
}
.wag-topbar-actions { display: flex; gap: 8px; }

/* ── Status pill ─────────────────────────────────────────────────*/
.wag-status-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 7px 14px;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 500;
    border: 1px solid;
}
.wag-pill-on {
    background: #f0fdf4; color: #166534; border-color: #bbf7d0;
}
.wag-pill-off {
    background: #fef2f2; color: #991b1b; border-color: #fecaca;
}
.wag-pill-dot {
    width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0;
}
.wag-pill-on .wag-pill-dot {
    background: #22c55e;
    box-shadow: 0 0 0 3px #bbf7d0;
    animation: wag-pulse 2s ease-in-out infinite;
}
.wag-pill-off .wag-pill-dot { background: #f87171; }

/* ── Buttons ─────────────────────────────────────────────────────*/
.wag-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 16px;
    border-radius: 9px;
    font-size: 13px; font-weight: 600;
    border: 1.5px solid transparent;
    cursor: pointer; transition: all 0.18s;
    white-space: nowrap;
}
.wag-btn:disabled { opacity: 0.55; cursor: not-allowed; }

.wag-btn-primary {
    background: #16a34a; color: white; border-color: #16a34a;
}
.wag-btn-primary:hover:not(:disabled) { background: #15803d; border-color: #15803d; }

.wag-btn-ghost {
    background: white; color: #374151; border-color: #e2e8f0;
}
.wag-btn-ghost:hover:not(:disabled) { background: #f1f5f9; border-color: #cbd5e1; }

.wag-btn-danger {
    background: #dc2626; color: white; border-color: #dc2626;
}
.wag-btn-danger:hover:not(:disabled) { background: #b91c1c; }

.wag-btn-warn {
    background: #d97706; color: white; border-color: #d97706;
    min-width: 80px; justify-content: center;
}
.wag-btn-warn:hover:not(:disabled) { background: #b45309; }

.wag-btn-sm { padding: 5px 10px; font-size: 12px; border-radius: 7px; }

/* ── States ──────────────────────────────────────────────────────*/
.wag-center-state {
    display: flex; flex-direction: column; align-items: center;
    justify-content: center; gap: 12px;
    padding: 5rem 2rem; color: #94a3b8; text-align: center;
}
.wag-empty-icon {
    width: 60px; height: 60px; background: #f1f5f9;
    border-radius: 50%; display: flex; align-items: center; justify-content: center;
    color: #cbd5e1;
}
.wag-state-title { font-size: 15px; font-weight: 600; color: #475569; margin: 0; }
.wag-state-text { font-size: 13px; color: #94a3b8; margin: 0; }

/* ── Grid ────────────────────────────────────────────────────────*/
.wag-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

/* ── Card ────────────────────────────────────────────────────────*/
.wag-card {
    background: white;
    border: 1.5px solid #e8ecf4;
    border-radius: 16px;
    padding: 1.25rem;
    transition: box-shadow 0.2s, border-color 0.2s;
}
.wag-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.07); }
.wag-card-active {
    border-color: #86efac;
    background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 60%);
}

.wag-card-head {
    display: flex; align-items: flex-start;
    justify-content: space-between; gap: 12px;
    margin-bottom: 1rem;
}
.wag-card-left { display: flex; align-items: center; gap: 12px; min-width: 0; }
.wag-card-right { flex-shrink: 0; }

.wag-avatar {
    width: 40px; height: 40px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.wag-avatar-on { background: #dcfce7; color: #16a34a; }
.wag-avatar-off { background: #f1f5f9; color: #94a3b8; }

.wag-session-name {
    font-size: 13px; font-weight: 700; color: #0f172a;
    word-break: break-all; line-height: 1.3;
}
.wag-session-date { font-size: 11px; color: #94a3b8; margin-top: 2px; }

/* Badges */
.wag-badge-active {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 11px; font-weight: 600;
    background: #dcfce7; color: #15803d;
    padding: 3px 10px; border-radius: 999px;
    border: 1px solid #bbf7d0;
}
.wag-badge-idle {
    display: inline-flex; align-items: center;
    font-size: 11px; font-weight: 500;
    background: #f8fafc; color: #94a3b8;
    padding: 3px 10px; border-radius: 999px;
    border: 1px solid #e2e8f0;
}
.wag-pulse {
    width: 6px; height: 6px; border-radius: 50%;
    background: #22c55e; display: inline-block;
    animation: wag-pulse 1.5s ease-in-out infinite;
}

/* Card actions */
.wag-card-actions { display: flex; gap: 8px; }

.wag-btn-action {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px;
    padding: 7px 12px; border-radius: 8px;
    font-size: 12px; font-weight: 600;
    border: 1.5px solid; cursor: pointer; transition: all 0.15s;
}
.wag-btn-action:disabled { opacity: 0.5; cursor: not-allowed; }

.wag-btn-qr {
    flex: 1;
    background: #f8fafc; color: #374151; border-color: #e2e8f0;
}
.wag-btn-qr:hover:not(:disabled) { background: #f1f5f9; border-color: #cbd5e1; }
.wag-btn-qr-open {
    background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe;
}

.wag-btn-del {
    background: #fff5f5; color: #dc2626; border-color: #fecaca;
    padding: 7px 10px;
}
.wag-btn-del:hover:not(:disabled) { background: #fee2e2; border-color: #fca5a5; }

/* ── QR Panel ────────────────────────────────────────────────────*/
.wag-qr-panel {
    margin-top: 1rem;
    padding: 1.25rem;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    text-align: center;
}
.wag-qr-instruction {
    font-size: 12px; color: #475569; line-height: 1.6;
    margin: 0 0 1rem;
}

.wag-qr-placeholder {
    width: 200px; height: 200px; margin: 0 auto 1rem;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center; gap: 10px;
    background: white; border-radius: 10px;
    border: 1.5px dashed #cbd5e1;
    color: #94a3b8; font-size: 12px;
}
.wag-qr-err { border-color: #fca5a5; color: #dc2626; background: #fff5f5; }

.wag-qr-frame {
    position: relative; width: 200px; height: 200px;
    margin: 0 auto 1rem;
}
.wag-qr-img {
    width: 100%; height: 100%; object-fit: contain;
    border-radius: 10px; display: block;
    border: 1px solid #e2e8f0;
}

/* Corner decorations */
.wag-qr-corner {
    position: absolute; width: 16px; height: 16px;
    border-color: #16a34a; border-style: solid;
}
.wag-qr-tl { top: -2px; left: -2px; border-width: 3px 0 0 3px; border-radius: 4px 0 0 0; }
.wag-qr-tr { top: -2px; right: -2px; border-width: 3px 3px 0 0; border-radius: 0 4px 0 0; }
.wag-qr-bl { bottom: -2px; left: -2px; border-width: 0 0 3px 3px; border-radius: 0 0 0 4px; }
.wag-qr-br { bottom: -2px; right: -2px; border-width: 0 3px 3px 0; border-radius: 0 0 4px 0; }

.wag-qr-footer {
    display: flex; align-items: center;
    justify-content: space-between; flex-wrap: wrap; gap: 8px;
    margin-top: 4px;
}
.wag-qr-note { font-size: 11px; color: #94a3b8; margin: 0; }

/* ── Guide ───────────────────────────────────────────────────────*/
.wag-guide {
    display: flex; gap: 12px;
    background: #f8fafc; border: 1px solid #e8ecf4;
    border-radius: 12px; padding: 1rem 1.25rem;
    margin-top: 1.5rem;
}
.wag-guide-icon {
    color: #3b82f6; flex-shrink: 0; margin-top: 2px;
}
.wag-guide-title {
    font-size: 12px; font-weight: 700; color: #1e293b;
    margin: 0 0 6px; text-transform: uppercase; letter-spacing: 0.05em;
}
.wag-guide-list {
    font-size: 13px; color: #475569;
    margin: 0; padding-left: 16px; line-height: 2.1;
}

/* ── Modal ───────────────────────────────────────────────────────*/
.wag-overlay {
    position: fixed; inset: 0;
    background: rgba(15, 23, 42, 0.5);
    display: flex; align-items: center; justify-content: center;
    z-index: 1000; backdrop-filter: blur(2px);
}
.wag-modal {
    background: white; border-radius: 18px;
    padding: 2rem; width: 360px; max-width: 90vw;
    text-align: center;
    box-shadow: 0 24px 64px rgba(0,0,0,0.18);
}
.wag-modal-icon {
    width: 48px; height: 48px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 1rem;
}
.wag-modal-danger { background: #fef2f2; color: #dc2626; }
.wag-modal-warn   { background: #fffbeb; color: #d97706; }
.wag-modal-title  { font-size: 1rem; font-weight: 700; color: #0f172a; margin: 0 0 6px; }
.wag-modal-desc   { font-size: 13px; color: #64748b; line-height: 1.6; margin: 0 0 1.5rem; }
.wag-modal-actions { display: flex; gap: 8px; }
.wag-modal-actions .wag-btn { flex: 1; justify-content: center; }

/* ── Toast ───────────────────────────────────────────────────────*/
.wag-toast {
    position: fixed; bottom: 1.5rem; right: 1.5rem;
    display: flex; align-items: center; gap: 8px;
    padding: 11px 18px; border-radius: 10px;
    font-size: 13px; font-weight: 500;
    box-shadow: 0 8px 28px rgba(0,0,0,0.14);
    z-index: 9999; max-width: 340px;
}
.wag-toast-success { background: #0f172a; color: white; }
.wag-toast-error   { background: #dc2626; color: white; }

/* ── Spinner ─────────────────────────────────────────────────────*/
.wag-ring {
    width: 32px; height: 32px;
    border: 3px solid #e2e8f0; border-top-color: #16a34a;
    border-radius: 50%; animation: wag-spin 0.75s linear infinite;
}
.wag-spin-sm {
    display: inline-block;
    width: 13px; height: 13px;
    border: 2px solid rgba(255,255,255,0.35); border-top-color: white;
    border-radius: 50%; animation: wag-spin 0.7s linear infinite;
}
.wag-spin-sm.dark {
    border-color: rgba(0,0,0,0.12); border-top-color: #475569;
}

/* ── Transitions ─────────────────────────────────────────────────*/
.qr-slide-enter-active,
.qr-slide-leave-active { transition: all 0.25s ease; overflow: hidden; }
.qr-slide-enter-from,
.qr-slide-leave-to { opacity: 0; transform: translateY(-6px); max-height: 0; }
.qr-slide-enter-to,
.qr-slide-leave-from { max-height: 500px; }

.modal-fade-enter-active,
.modal-fade-leave-active { transition: opacity 0.2s; }
.modal-fade-enter-from,
.modal-fade-leave-to { opacity: 0; }

.toast-slide-enter-active,
.toast-slide-leave-active { transition: all 0.3s ease; }
.toast-slide-enter-from,
.toast-slide-leave-to { opacity: 0; transform: translateY(10px); }

/* ── Keyframes ───────────────────────────────────────────────────*/
@keyframes wag-spin  { to { transform: rotate(360deg); } }
@keyframes wag-pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.35; } }

/* ── Responsive ──────────────────────────────────────────────────*/
@media (max-width: 560px) {
    .wag-page { padding: 1.25rem 1rem 3rem; }
    .wag-topbar { flex-direction: column; align-items: flex-start; }
    .wag-grid { grid-template-columns: 1fr; }
    .wag-qr-footer { flex-direction: column; align-items: center; }
}
</style>