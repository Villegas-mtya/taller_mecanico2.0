<template>
  <section class="login-page">
    <div class="login-card">
      <p class="eyebrow">Acceso</p>
      <h1>Iniciar sesión</h1>
      <p class="subtitle">
        Aquí irá el formulario de autenticación del sistema.
      </p>

      <form class="login-form">
        <div class="field">
          <label for="correo">Correo</label>
          <input id="correo" type="email" class="input" placeholder="correo@ejemplo.com" />
        </div>

        <div class="field">
          <label for="password">Contraseña</label>
          <input id="password" type="password" class="input" placeholder="••••••••" />
        </div>

        <button type="button" class="btn btn-primary">Entrar</button>
      </form>
    </div>
  </section>
</template>

<style scoped>
.login-page {
  width: 100%;
  max-width: 420px;
}

.login-card {
  padding: 24px;
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-md);
}

.eyebrow {
  margin: 0 0 8px;
  color: var(--color-primary);
  font-size: 0.8rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

h1 {
  margin: 0 0 10px;
  color: var(--color-text);
}

.subtitle {
  margin: 0 0 20px;
  color: var(--color-text-muted);
}

.login-form {
  display: grid;
  gap: 16px;
}

.field {
  display: grid;
}

.field label {
  margin-bottom: 8px;
  color: var(--color-text);
  font-weight: 600;
}

.input {
  width: 100%;
  min-height: 48px;
  padding: 12px 14px;
  background: var(--color-input-bg);
  color: var(--color-text);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  outline: none;
}

.input:focus {
  border-color: var(--color-primary);
  box-shadow: 0 0 0 4px var(--color-focus-ring);
}

.btn {
  min-height: 44px;
  padding: 10px 16px;
  border: 1px solid transparent;
  border-radius: var(--radius-md);
  font-weight: 600;
  cursor: pointer;
}

.btn-primary {
  background: var(--color-primary);
  color: var(--color-white);
}
</style>