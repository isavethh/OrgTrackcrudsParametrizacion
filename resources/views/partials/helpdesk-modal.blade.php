<!-- Helpdesk Modal -->
<div class="modal fade" id="helpdeskModal" tabindex="-1" role="dialog" aria-labelledby="helpdeskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document" style="max-height: 90vh;">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="helpdeskModalLabel">
                    <i class="fas fa-headset mr-2"></i>Centro de Soporte
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0" style="height: 70vh; overflow: hidden;">
                {{-- 
                    El widget personalizado se carga aquí. 
                    Implementa mapeo estricto según documentación.
                --}}
                <x-custom-helpdesk-widget width="100%" height="100%" />
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Buscar el enlace del sidebar que tenga el texto "Centro de Soporte" o el href "#helpdesk"
        // AdminLTE a veces modifica los hrefs, así que buscamos por contenido también.
        const sidebarLinks = document.querySelectorAll('.nav-sidebar .nav-link');
        
        sidebarLinks.forEach(link => {
            const text = link.innerText.trim();
            const href = link.getAttribute('href');
            
            if (text === 'Centro de Soporte' || (href && href.includes('#helpdesk'))) {
                // Sobrescribir comportamiento
                link.setAttribute('href', '#');
                link.setAttribute('data-toggle', 'modal');
                link.setAttribute('data-target', '#helpdeskModal');
                
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    $('#helpdeskModal').modal('show');
                });
                console.log('✅ Helpdesk link hooked successfully');
            }
        });
        
        // Soporte para abrir directo si vienes de una URL con hash
        if(window.location.hash === '#helpdesk') {
            $('#helpdeskModal').modal('show');
        }
    });

    // Ajustar iframe cuando se muestra el modal
    $('#helpdeskModal').on('shown.bs.modal', function () {
        // Forzar un pequeño reflow o resize si es necesario
    });
</script>
