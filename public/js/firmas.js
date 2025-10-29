// Configuración de la API
const API_CONFIG = {
    baseUrl: 'http://localhost:8000/api',
    endpoints: {
        auth: '/usuarios',
        firmas: {
            transportista: '/firmas/transportista',
            cliente: '/firmas/envio'
        }
    }
};

// Clase para manejar las firmas
class SignatureManager {
    constructor() {
        this.currentUser = null;
        this.signaturePads = {};
        this.init();
    }

    init() {
        this.initializeSignaturePads();
        this.setupEventListeners();
    }

    // Inicializar los pads de firma
    initializeSignaturePads() {
        const canvasTransportista = document.getElementById('signaturePadTransportista');
        const canvasCliente = document.getElementById('signaturePadCliente');

        [canvasTransportista, canvasCliente].forEach(canvas => {
            if (canvas) {
                this.setupCanvas(canvas);
                this.signaturePads[canvas.id] = canvas;
            }
        });
    }

    // Configurar canvas
    setupCanvas(canvas) {
        const ctx = canvas.getContext('2d');
        ctx.strokeStyle = '#000';
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';

        let isDrawing = false;
        let lastX = 0;
        let lastY = 0;

        // Eventos del mouse
        canvas.addEventListener('mousedown', (e) => {
            isDrawing = true;
            [lastX, lastY] = this.getMousePos(canvas, e);
        });

        canvas.addEventListener('mousemove', (e) => {
            if (!isDrawing) return;
            const [currentX, currentY] = this.getMousePos(canvas, e);
            this.drawLine(ctx, lastX, lastY, currentX, currentY);
            [lastX, lastY] = [currentX, currentY];
        });

        canvas.addEventListener('mouseup', () => isDrawing = false);
        canvas.addEventListener('mouseout', () => isDrawing = false);

        // Eventos táctiles
        canvas.addEventListener('touchstart', (e) => {
            e.preventDefault();
            isDrawing = true;
            const touch = e.touches[0];
            [lastX, lastY] = this.getTouchPos(canvas, touch);
        });

        canvas.addEventListener('touchmove', (e) => {
            e.preventDefault();
            if (!isDrawing) return;
            const touch = e.touches[0];
            const [currentX, currentY] = this.getTouchPos(canvas, touch);
            this.drawLine(ctx, lastX, lastY, currentX, currentY);
            [lastX, lastY] = [currentX, currentY];
        });

        canvas.addEventListener('touchend', (e) => {
            e.preventDefault();
            isDrawing = false;
        });
    }

    // Obtener posición del mouse
    getMousePos(canvas, e) {
        const rect = canvas.getBoundingClientRect();
        return [e.clientX - rect.left, e.clientY - rect.top];
    }

    // Obtener posición del touch
    getTouchPos(canvas, touch) {
        const rect = canvas.getBoundingClientRect();
        return [touch.clientX - rect.left, touch.clientY - rect.top];
    }

    // Dibujar línea
    drawLine(ctx, x1, y1, x2, y2) {
        ctx.beginPath();
        ctx.moveTo(x1, y1);
        ctx.lineTo(x2, y2);
        ctx.stroke();
    }

    // Configurar event listeners
    setupEventListeners() {
        // Botón de autenticación
        const authBtn = document.querySelector('[onclick="authenticateUser()"]');
        if (authBtn) {
            authBtn.addEventListener('click', () => this.authenticateUser());
        }

        // Botones de limpiar firma
        document.querySelectorAll('[onclick*="clearSignature"]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const type = e.target.getAttribute('onclick').match(/clearSignature\('(\w+)'\)/)[1];
                this.clearSignature(type);
            });
        });

        // Botones de guardar firma
        document.querySelectorAll('[onclick*="saveSignature"]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const type = e.target.getAttribute('onclick').match(/saveSignature\('(\w+)'\)/)[1];
                this.saveSignature(type);
            });
        });
    }

    // Autenticar usuario
    async authenticateUser() {
        const token = document.getElementById('token').value.trim();
        if (!token) {
            this.showAlert('Por favor ingresa un token válido', 'error');
            return;
        }

        this.showLoading(true);
        try {
            const response = await fetch(`${API_CONFIG.baseUrl}${API_CONFIG.endpoints.auth}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                }
            });

            if (response.ok) {
                // Token válido, simular datos del usuario
                this.currentUser = {
                    id: 1,
                    nombre: 'Usuario',
                    rol: 'transportista' // o 'cliente' según el token
                };
                
                this.showUserInfo();
                this.showSections();
                this.showAlert('Token verificado correctamente', 'success');
            } else {
                throw new Error('Token inválido');
            }
        } catch (error) {
            this.showAlert('Error al verificar el token: ' + error.message, 'error');
        } finally {
            this.showLoading(false);
        }
    }

    // Mostrar información del usuario
    showUserInfo() {
        if (this.currentUser) {
            document.getElementById('userName').textContent = this.currentUser.nombre;
            document.getElementById('userRole').textContent = this.currentUser.rol;
            document.getElementById('userId').textContent = this.currentUser.id;
            document.getElementById('userInfo').style.display = 'block';
        }
    }

    // Mostrar secciones según el rol
    showSections() {
        if (this.currentUser) {
            if (this.currentUser.rol === 'transportista') {
                document.getElementById('transportistaSection').style.display = 'block';
            } else if (this.currentUser.rol === 'cliente') {
                document.getElementById('clienteSection').style.display = 'block';
            }
            document.getElementById('verificationSection').style.display = 'block';
        }
    }

    // Limpiar firma
    clearSignature(type) {
        const canvas = document.getElementById(`signaturePad${type.charAt(0).toUpperCase() + type.slice(1)}`);
        if (canvas) {
            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            document.getElementById(`preview${type.charAt(0).toUpperCase() + type.slice(1)}`).innerHTML = '';
        }
    }

    // Guardar firma
    async saveSignature(type) {
        const canvas = document.getElementById(`signaturePad${type.charAt(0).toUpperCase() + type.slice(1)}`);
        const asignacionId = document.getElementById(`asignacion${type.charAt(0).toUpperCase() + type.slice(1)}`).value;

        if (!asignacionId) {
            this.showAlert('Por favor ingresa el ID de asignación', 'error');
            return;
        }

        if (canvas.toDataURL() === canvas.toDataURL('image/png', 1.0)) {
            this.showAlert('Por favor realiza una firma antes de guardar', 'error');
            return;
        }

        const signatureData = canvas.toDataURL('image/png');
        
        this.showLoading(true);
        try {
            const endpoint = type === 'transportista' ? 
                `${API_CONFIG.baseUrl}${API_CONFIG.endpoints.firmas.transportista}/${asignacionId}` : 
                `${API_CONFIG.baseUrl}${API_CONFIG.endpoints.firmas.cliente}/${asignacionId}`;

            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${document.getElementById('token').value}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    imagenFirma: signatureData
                })
            });

            if (response.ok) {
                this.showAlert(`Firma del ${type} guardada correctamente`, 'success');
                this.showSignaturePreview(type, signatureData);
            } else {
                const error = await response.json();
                throw new Error(error.error || 'Error al guardar la firma');
            }
        } catch (error) {
            this.showAlert('Error al guardar la firma: ' + error.message, 'error');
        } finally {
            this.showLoading(false);
        }
    }

    // Mostrar preview de la firma
    showSignaturePreview(type, signatureData) {
        const preview = document.getElementById(`preview${type.charAt(0).toUpperCase() + type.slice(1)}`);
        preview.innerHTML = `
            <h4>Firma guardada:</h4>
            <img src="${signatureData}" alt="Firma ${type}">
        `;
    }

    // Mostrar loading
    showLoading(show) {
        document.getElementById('loading').style.display = show ? 'block' : 'none';
    }

    // Mostrar alertas
    showAlert(message, type) {
        const alertsContainer = document.getElementById('alerts');
        const alertClass = type === 'error' ? 'alert-error' : 
                          type === 'success' ? 'alert-success' : 'alert-info';
        
        const alert = document.createElement('div');
        alert.className = `alert ${alertClass}`;
        alert.textContent = message;
        
        alertsContainer.appendChild(alert);
        
        // Remover después de 5 segundos
        setTimeout(() => {
            alert.remove();
        }, 5000);
    }
}

// Inicializar cuando se carga la página
document.addEventListener('DOMContentLoaded', function() {
    new SignatureManager();
});

