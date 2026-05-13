<script setup>
/**
 * NotificationBell.vue
 * Polling-based notification — no WebSocket/Socket.io
 * Interval: 30 detik. Endpoint: /api/notifications
 */
import { ref, onMounted, onUnmounted, computed } from "vue";

// ── State ──────────────────────────────────────────────────
const notifications = ref([]);
const open          = ref(false);
const loading       = ref(false);
const POLL_MS       = 30_000;           // 30 detik
let   pollTimer     = null;

// ── Computed ───────────────────────────────────────────────
const unread = computed(() => notifications.value.filter(n => !n.read).length);

// ── Fetch ──────────────────────────────────────────────────
async function fetchNotifications() {
    loading.value = true;
    try {
        const res  = await fetch("/api/notifications");
        const data = await res.json();
        // Pertahankan state 'read' lokal untuk item yang sudah ada
        const existing = Object.fromEntries(
            notifications.value.map(n => [n.id, n.read])
        );
        notifications.value = (data.notifications ?? []).map(n => ({
            ...n,
            read: existing[n.id] ?? n.read ?? false,
        }));
    } catch (e) {
        console.warn("[NotificationBell] fetch error:", e);
    } finally {
        loading.value = false;
    }
}

// ── Toggle panel ───────────────────────────────────────────
function toggle() {
    open.value = !open.value;
    if (open.value) markAllRead();
}

function markAllRead() {
    notifications.value = notifications.value.map(n => ({ ...n, read: true }));
}

function close() { open.value = false; }

// ── Click outside ──────────────────────────────────────────
function onOutside(e) {
    if (!e.target.closest(".nb-root")) close();
}

// ── Lifecycle ──────────────────────────────────────────────
onMounted(() => {
    fetchNotifications();
    pollTimer = setInterval(fetchNotifications, POLL_MS);
    document.addEventListener("mousedown", onOutside);
});

onUnmounted(() => {
    clearInterval(pollTimer);
    document.removeEventListener("mousedown", onOutside);
});

// ── Helpers ────────────────────────────────────────────────
function iconFor(type) {
    const icons = {
        forward : `<path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>`,
        selesai : `<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>`,
        baru    : `<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>`,
        default : `<path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>`,
    };
    return icons[type] ?? icons.default;
}

function colorFor(type) {
    return { forward:"icon-amber", selesai:"icon-green", baru:"icon-blue" }[type] ?? "icon-slate";
}
</script>

<template>
    <div class="nb-root">

        <!-- ── Bell Button ── -->
        <button
            class="nb-btn"
            :class="{ 'nb-btn--active': open }"
            @click="toggle"
            :title="`${unread} notifikasi belum dibaca`"
        >
            <svg
                class="nb-icon"
                :class="{ 'nb-ring': unread > 0 }"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                viewBox="0 0 24 24"
            >
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>

            <!-- Badge -->
            <span v-if="unread > 0" class="nb-badge">
                {{ unread > 9 ? "9+" : unread }}
            </span>
        </button>

        <!-- ── Dropdown Panel ── -->
        <Transition name="nb-drop">
            <div v-if="open" class="nb-panel">

                <!-- Header -->
                <div class="nb-panel-head">
                    <span class="nb-panel-title">Notifikasi</span>
                    <div class="nb-head-right">
                        <!-- Loading spinner -->
                        <svg v-if="loading" class="nb-spin" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v3m9-9h-3M3 12H6"/>
                        </svg>
                        <!-- Unread count pill -->
                        <span v-if="unread > 0" class="nb-unread-pill">{{ unread }} baru</span>
                    </div>
                </div>

                <!-- Empty -->
                <div v-if="notifications.length === 0 && !loading" class="nb-empty">
                    <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <p>Tidak ada notifikasi</p>
                </div>

                <!-- Skeleton while loading first time -->
                <template v-else-if="loading && notifications.length === 0">
                    <div class="nb-sk-item" v-for="i in 4" :key="i">
                        <div class="nb-sk nb-sk-icon"></div>
                        <div style="flex:1;display:flex;flex-direction:column;gap:0.35rem">
                            <div class="nb-sk" style="width:70%;height:10px"></div>
                            <div class="nb-sk" style="width:45%;height:9px"></div>
                        </div>
                    </div>
                </template>

                <!-- List -->
                <ul v-else class="nb-list">
                    <li
                        v-for="n in notifications"
                        :key="n.id"
                        class="nb-item"
                        :class="{ 'nb-item--unread': !n.read }"
                    >
                        <!-- Icon -->
                        <div class="nb-item-icon" :class="colorFor(n.type)">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" v-html="iconFor(n.type)"/>
                        </div>

                        <!-- Text -->
                        <div class="nb-item-body">
                            <p class="nb-item-title">{{ n.title }}</p>
                            <p class="nb-item-desc">{{ n.body }}</p>
                            <p class="nb-item-time">{{ n.time }}</p>
                        </div>

                        <!-- Unread dot -->
                        <span v-if="!n.read" class="nb-dot"></span>
                    </li>
                </ul>

                <!-- Footer -->
                <div class="nb-panel-foot">
                    <span class="nb-poll-info">
                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Diperbarui tiap 30 detik
                    </span>
                    <button class="nb-refresh" @click="fetchNotifications">Muat ulang</button>
                </div>

            </div>
        </Transition>
    </div>
</template>

<style scoped>
/* ── Root ── */
.nb-root {
    position: relative;
    display: flex;
    align-items: center;
}

/* ── Button ── */
.nb-btn {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 9px;
    border: none;
    background: transparent;
    cursor: pointer;
    color: #64748b;
    transition: background 0.12s, color 0.12s;
}
.nb-btn:hover,
.nb-btn--active {
    background: #f1f5f9;
    color: #1e293b;
}
:is(.dark .nb-btn):hover,
:is(.dark .nb-btn--active) {
    background: rgba(255,255,255,0.08);
    color: #e2e8f0;
}

.nb-icon {
    width: 20px;
    height: 20px;
    transition: transform 0.2s;
}

/* Ring animation for new notifications */
.nb-ring {
    animation: nb-bell 2.4s ease-in-out infinite;
}
@keyframes nb-bell {
    0%,100% { transform: rotate(0); }
    5%       { transform: rotate(12deg); }
    10%      { transform: rotate(-10deg); }
    15%      { transform: rotate(8deg); }
    20%      { transform: rotate(0); }
}

/* Badge */
.nb-badge {
    position: absolute;
    top: 4px;
    right: 4px;
    min-width: 16px;
    height: 16px;
    padding: 0 3.5px;
    background: #ef4444;
    color: #fff;
    font-size: 9.5px;
    font-weight: 700;
    border-radius: 999px;
    display: flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
    border: 1.5px solid #fff;
}
:is(.dark .nb-badge) { border-color: #111827; }

/* ── Panel ── */
.nb-panel {
    position: absolute;
    top: calc(100% + 10px);
    right: 0;
    width: 340px;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.1), 0 2px 8px rgba(0,0,0,0.06);
    z-index: 200;
    overflow: hidden;
}
.dark .nb-panel {
    background: #1e293b;
    border-color: #334155;
    box-shadow: 0 8px 30px rgba(0,0,0,0.4);
}

/* Panel header */
.nb-panel-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.9rem 1.1rem 0.75rem;
    border-bottom: 1px solid #f1f5f9;
}
.dark .nb-panel-head { border-color: #334155; }

.nb-panel-title {
    font-size: 0.85rem;
    font-weight: 700;
    color: #0f172a;
}
.dark .nb-panel-title { color: #f1f5f9; }

.nb-head-right { display: flex; align-items: center; gap: 0.5rem; }

.nb-spin {
    color: #94a3b8;
    animation: spin 0.75s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

.nb-unread-pill {
    font-size: 0.68rem;
    font-weight: 700;
    color: #1d4ed8;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    padding: 0.15rem 0.5rem;
    border-radius: 999px;
}

/* Empty */
.nb-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    padding: 2rem 1rem;
    color: #94a3b8;
    font-size: 0.78rem;
}

/* Skeleton */
.nb-sk-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.85rem 1.1rem;
    border-bottom: 1px solid #f8fafc;
}
.nb-sk {
    background: #f1f5f9;
    border-radius: 4px;
    animation: shimmer 1.3s ease-in-out infinite;
}
.nb-sk-icon { width: 32px; height: 32px; border-radius: 8px; flex-shrink: 0; }
@keyframes shimmer { 0%,100%{opacity:1} 50%{opacity:0.4} }

/* List */
.nb-list {
    list-style: none;
    margin: 0;
    padding: 0;
    max-height: 320px;
    overflow-y: auto;
}
.nb-list::-webkit-scrollbar { width: 4px; }
.nb-list::-webkit-scrollbar-track { background: transparent; }
.nb-list::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 2px; }

.nb-item {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 0.85rem 1.1rem;
    border-bottom: 1px solid #f8fafc;
    transition: background 0.1s;
    position: relative;
}
.nb-item:last-child { border-bottom: none; }
.nb-item:hover { background: #f8fafc; }
.nb-item--unread { background: #f0f7ff; }
.nb-item--unread:hover { background: #e8f0fe; }

.dark .nb-item { border-color: #1e293b; }
.dark .nb-item:hover { background: rgba(255,255,255,0.04); }
.dark .nb-item--unread { background: rgba(59,130,246,0.08); }

/* Item icon */
.nb-item-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    margin-top: 0.05rem;
}
.icon-amber { background: #fffbeb; color: #b45309; }
.icon-blue  { background: #eff6ff; color: #1d4ed8; }
.icon-green { background: #f0fdf4; color: #15803d; }
.icon-slate { background: #f1f5f9; color: #64748b; }

/* Item body */
.nb-item-body { flex: 1; min-width: 0; }
.nb-item-title {
    font-size: 0.8rem;
    font-weight: 600;
    color: #0f172a;
    line-height: 1.35;
    margin-bottom: 0.15rem;
}
.dark .nb-item-title { color: #e2e8f0; }
.nb-item-desc {
    font-size: 0.74rem;
    color: #64748b;
    line-height: 1.5;
    margin-bottom: 0.25rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.nb-item-time {
    font-size: 0.68rem;
    color: #94a3b8;
    font-family: 'DM Mono', monospace;
}

/* Unread dot */
.nb-dot {
    width: 7px;
    height: 7px;
    background: #3b82f6;
    border-radius: 50%;
    flex-shrink: 0;
    margin-top: 0.35rem;
}

/* Footer */
.nb-panel-foot {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.65rem 1.1rem;
    border-top: 1px solid #f1f5f9;
    background: #f8fafc;
}
.dark .nb-panel-foot { background: #0f172a; border-color: #1e293b; }

.nb-poll-info {
    display: flex;
    align-items: center;
    gap: 0.3rem;
    font-size: 0.67rem;
    color: #94a3b8;
}
.nb-refresh {
    font-size: 0.72rem;
    font-weight: 600;
    color: #1d4ed8;
    background: none;
    border: none;
    cursor: pointer;
    padding: 0.2rem 0.4rem;
    border-radius: 5px;
    transition: background 0.12s;
}
.nb-refresh:hover { background: #eff6ff; }

/* ── Dropdown transition ── */
.nb-drop-enter-active {
    transition: all 0.18s cubic-bezier(0.16, 1, 0.3, 1);
}
.nb-drop-leave-active {
    transition: all 0.12s ease;
}
.nb-drop-enter-from,
.nb-drop-leave-to {
    opacity: 0;
    transform: translateY(-8px) scale(0.97);
}
</style>