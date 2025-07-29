<div class="row g-0 text-center text-md-start align-items-center mt-3">
    <div class="col-sm-12 col-md-5">
        Showing {{ $items->firstItem() ?? 0 }} to {{ $items->lastItem() ?? 0 }} of {{ $items->total() }} entries
    </div>
    <div class="col-sm-12 col-md-7">
        <div class="d-flex justify-content-center justify-content-md-end">
            {{ $items->links() }}
        </div>
    </div>
</div>
