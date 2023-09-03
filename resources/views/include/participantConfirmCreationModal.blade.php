<div class="modal fade" id="createAccountsModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Maak accounts voor alle deelnemers</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Creeër salve mundi office accounts en coupon codes zodat ze hun account met een betaling van 1 cent kunnen activeren op salvemundi.nl
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <form method="POST" action="/participants/createAccounts">
                    @csrf
                    <button type="submit" class="btn btn-primary">Maak accounts</button>
                </form>
            </div>
        </div>
    </div>
</div>
