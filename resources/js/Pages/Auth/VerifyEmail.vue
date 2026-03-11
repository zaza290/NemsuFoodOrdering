<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: { type: String },
});

const form = useForm({});
const submit = () => { form.post(route('verification.send')); };
const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <Head title="Verify Email" />

    <div class="auth-root">
        <div class="auth-left">
            <div class="auth-left-content">
                <div class="left-logo">🍽️</div>
                <h1 class="left-title">NEMSU<br>Chow Zone</h1>
                <p class="left-sub">Your campus food ordering system</p>
                <div class="steps">
                    <div class="step done">✅ Account Created</div>
                    <div class="step active">📧 Verify Email</div>
                    <div class="step">🍔 Start Ordering</div>
                </div>
            </div>
        </div>

        <div class="auth-right">
            <div class="auth-card">
                <div class="icon-wrap">📬</div>
                <h2>Check your inbox!</h2>
                <p class="subtitle">
                    Thanks for signing up! We sent a verification link to your email.
                    Click it to activate your NEMSU Chow Zone account.
                </p>

                <div v-if="verificationLinkSent" class="success-msg">
                    ✅ A new verification link has been sent to your email address!
                </div>

                <div class="info-box">
                    <span class="info-icon">💡</span>
                    <span>Can't find it? Check your <strong>spam or junk folder</strong>, or resend below.</span>
                </div>

                <form @submit.prevent="submit">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="btn-submit"
                    >
                        <span v-if="form.processing" class="loading-spinner"></span>
                        {{ form.processing ? 'Sending...' : '📨 Resend Verification Email' }}
                    </button>
                </form>

                <div class="divider"></div>

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="btn-logout"
                >
                    Log Out
                </Link>
            </div>
        </div>
    </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500;600&display=swap');
* { box-sizing: border-box; margin: 0; padding: 0; }

.auth-root {
    display: flex;
    min-height: 100vh;
    font-family: 'DM Sans', sans-serif;
}

.auth-left {
    width: 42%;
    background: linear-gradient(160deg, #064e3b, #047857);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 3rem;
    position: relative;
    overflow: hidden;
}
.auth-left::before {
    content: '';
    position: absolute;
    width: 400px; height: 400px;
    background: rgba(16,185,129,0.2);
    border-radius: 50%;
    bottom: -100px; right: -100px;
}
.auth-left-content { position: relative; z-index: 1; color: white; }
.left-logo { font-size: 4rem; margin-bottom: 1rem; }
.left-title {
    font-family: 'Syne', sans-serif;
    font-size: 3rem; font-weight: 800;
    line-height: 1.1; margin-bottom: 0.75rem;
}
.left-sub { color: #a7f3d0; font-size: 0.95rem; margin-bottom: 2rem; }

.steps { display: flex; flex-direction: column; gap: 0.75rem; }
.step {
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.15);
    border-radius: 10px;
    padding: 0.65rem 1rem;
    font-size: 0.875rem;
    color: rgba(255,255,255,0.5);
}
.step.done { color: #6ee7b7; border-color: rgba(110,231,183,0.3); background: rgba(110,231,183,0.1); }
.step.active { color: #fff; border-color: rgba(255,255,255,0.4); background: rgba(255,255,255,0.12); font-weight: 600; }

.auth-right {
    flex: 1;
    background: #f9fafb;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}

.auth-card {
    background: white;
    border-radius: 20px;
    padding: 2.5rem;
    width: 100%;
    max-width: 420px;
    box-shadow: 0 4px 40px rgba(0,0,0,0.08);
    text-align: center;
}

.icon-wrap {
    font-size: 3.5rem;
    margin-bottom: 1rem;
    display: block;
}

h2 {
    font-family: 'Syne', sans-serif;
    font-size: 1.8rem; font-weight: 800;
    color: #064e3b;
    margin-bottom: 0.75rem;
}

.subtitle {
    color: #6b7280;
    font-size: 0.9rem;
    line-height: 1.6;
    margin-bottom: 1.5rem;
}

.success-msg {
    background: #d1fae5;
    color: #065f46;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    font-size: 0.875rem;
    margin-bottom: 1.25rem;
    font-weight: 600;
}

.info-box {
    display: flex;
    align-items: flex-start;
    gap: 0.6rem;
    background: #fefce8;
    border: 1px solid #fde68a;
    border-radius: 10px;
    padding: 0.85rem 1rem;
    font-size: 0.85rem;
    color: #92400e;
    text-align: left;
    margin-bottom: 1.5rem;
}
.info-icon { font-size: 1rem; flex-shrink: 0; margin-top: 1px; }

.btn-submit {
    width: 100%;
    background: linear-gradient(135deg, #10b981, #065f46);
    color: white;
    font-weight: 700;
    font-size: 0.95rem;
    font-family: 'DM Sans', sans-serif;
    padding: 0.85rem;
    border-radius: 12px;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: all 0.2s;
}
.btn-submit:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(16,185,129,0.4); }
.btn-submit:disabled { opacity: 0.6; cursor: not-allowed; }

.loading-spinner {
    width: 16px; height: 16px;
    border: 2px solid rgba(255,255,255,0.3);
    border-top-color: white;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

.divider {
    height: 1px;
    background: #e5e7eb;
    margin: 1.5rem 0;
}

.btn-logout {
    background: none;
    border: none;
    color: #9ca3af;
    font-size: 0.875rem;
    font-family: 'DM Sans', sans-serif;
    cursor: pointer;
    text-decoration: underline;
    transition: color 0.2s;
}
.btn-logout:hover { color: #6b7280; }

@media (max-width: 768px) {
    .auth-left { display: none; }
    .auth-root { background: #f9fafb; }
}
</style>
