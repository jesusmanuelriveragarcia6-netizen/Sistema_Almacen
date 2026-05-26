<div class="cortex-floater" onclick="openCortexAssistant()" title="Hablar con Cortex Assistant">
    <div class="floater-orb">
        <i class="fa-solid fa-brain-circuit"></i>
    </div>
</div>

<!-- CORTEX MODAL -->
<div class="modal fade cortex-modal-blur" id="cortexModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content cortex-modal-content">
            <div class="modal-body p-0">
                <div class="d-flex justify-content-end p-4 position-absolute" style="right: 0; z-index: 10;">
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.5; font-size: 2rem;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                {{ $slot }}
            </div>
        </div>
    </div>
</div>

<script>
function openCortexAssistant() {
    $('#cortexModal').modal('show');
}
</script>
