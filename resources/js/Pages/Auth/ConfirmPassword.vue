<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const showPass = ref(false);

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Confirm Password" />

    <div class="auth-root">
        <div class="auth-left">
            <div class="auth-left-content">
                <div class="left-logo">🍽️</div>
                <h1 class="left-title">NEMSU<br>Chow Zone</h1>
                <p class="left-sub">Your campus food ordering system</p>
                <div class="security-note">
                    <div class="security-icon">🛡️</div>
                    <p>This is a <strong>secure area</strong>. We verify your identity before allowing access to sensitive actions.</p>
                </div>
            </div>
        </div>

        <div class="auth-right">
            <div class="auth-card">
                <div class="auth-header">
                    <div class="icon-wrap">🔐</div>
                    <h2>Confirm Password</h2>
                    <p>This is a secure area. Please re-enter your password to continue.</p>
                </div>

                <form @submit.prevent="submit" class="auth-form">
                    <div class="form-group">
                        <label>Password</label>
                        <div class="input-wrap">
                            <span class="input-icon">🔒</span>
                            <input
                                :type="showPass ? 'text' : 'password'"
                                v-model="form.password"
                                required
                                autocomplete="current-password"
                                autofocus
                                placeholder="Enter your password"
                                :class="['form-input', { 'input-error': form.errors.password }]"
                            />
                            <button type="button" @click="showPass = !showPass" class="pass-toggle">
                                {{ showPass ? '🙈' : '👁️' }}
                            </button>
                        </div>
                        <span v-if="form.errors.password" class="error-msg">{{ form.errors.password }}</span>
                    </div>

                    <button type="submit" :disabled="form.processing" class="btn-submit">
                        <span v-if="form.processing" class="loading-spinner"></span>
                        {{ form.processing ? 'Confirming...' : 'Confirm & Continue 🔐' }}
                    </button>
                </form>
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

.security-note {
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 14px;
    padding: 1.25rem;
    display: flex;
    gap: 0.75rem;
    align-items: flex-start;
}
.security-icon { font-size: 1.75rem; flex-shrink: 0; }
.security-note p { font-size: 0.875rem; color: #d1fae5; line-height: 1.5; }
.security-note strong { color: #fff; }

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
}

.auth-header { text-align: center; margin-bottom: 2rem; }
.icon-wrap { font-size: 3rem; display: block; margin-bottom: 0.75rem; }
h2 {
    font-family: 'Syne', sans-serif;
    font-size: 1.8rem; font-weight: 800;
    color: #064e3b; margin-bottom: 0.5rem;
}
.auth-header p { color: #6b7280; font-size: 0.9rem; line-height: 1.5; }

.auth-form { display: flex; flex-direction: column; gap: 1.25rem; }
.form-group { display: flex; flex-direction: column; gap: 0.4rem; }
.form-group label { font-size: 0.875rem; font-weight: 600; color: #374151; }

.input-wrap { position: relative; }
.input-icon {
    position: absolute; left: 0.85rem; top: 50%;
    transform: translateY(-50%); font-size: 1rem;
    pointer-events: none;
}
.form-input {
    width: 100%;
    padding: 0.75rem 2.75rem;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    font-size: 0.9rem;
    font-family: 'DM Sans', sans-serif;
    transition: border-color 0.2s;
    color: #111827;
}
.form-input:focus { outline: none; border-color: #10b981; box-shadow: 0 0 0 3px rgba(16,185,129,0.1); }
.input-error { border-color: #ef4444 !important; }
.error-msg { color: #ef4444; font-size: 0.78rem; }

.pass-toggle {
    position: absolute; right: 0.85rem; top: 50%;
    transform: translateY(-50%);
    background: none; border: none; cursor: pointer; font-size: 1rem;
}

.btn-submit {
    width: 100%;
    background: linear-gradient(135deg, #10b981, #065f46);
    color: white; font-weight: 700; font-size: 1rem;
    font-family: 'DM Sans', sans-serif;
    padding: 0.85rem; border-radius: 12px; border: none;
    cursor: pointer; display: flex; align-items: center;
    justify-content: center; gap: 0.5rem; transition: all 0.2s;
}
.btn-submit:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(16,185,129,0.4); }
.btn-submit:disabled { opacity: 0.6; cursor: not-allowed; }

.loading-spinner {
    width: 16px; height: 16px;
    border: 2px solid rgba(255,255,255,0.3);
    border-top-color: white; border-radius: 50%;
    animation: spin 0.8s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

@media (max-width: 768px) {
    .auth-left { display: none; }
    .auth-root { background: #f9fafb; }
}
</style>
