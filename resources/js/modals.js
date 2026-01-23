export function detailModal(modalName) {
    return {
        loading: false,
        error: null,
        data: null,

        async open(url) {
            this.loading = true;
            this.error = null;
            this.data = null;

            window.dispatchEvent(new CustomEvent('open-modal', { detail: modalName }));

            try {
                const res = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin', // ✅ CLAVE: envía cookie de sesión
                });

                // Si Laravel redirige al login, normalmente termina en HTML
                const ct = res.headers.get('content-type') || '';

                if (!res.ok) {
                    throw new Error(`No se pudo cargar el detalle (HTTP ${res.status})`);
                }

                if (!ct.includes('application/json')) {
                    // Debug útil sin romper JSON parse
                    const text = await res.text();
                    throw new Error(
                        'Respuesta no JSON (probable redirect/login). ' +
                        'Revisa APP_URL (localhost vs 127.0.0.1). ' +
                        'Inicio respuesta: ' + text.slice(0, 30)
                    );
                }

                this.data = await res.json();
            } catch (e) {
                this.error = e?.message ?? 'Error inesperado';
            } finally {
                this.loading = false;
            }
        },

        close() {
            window.dispatchEvent(new CustomEvent('close-modal', { detail: modalName }));
            this.loading = false;
            this.error = null;
            this.data = null;
        },
    };
}
