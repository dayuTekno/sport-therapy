<div 
    x-data="{ showAlert: false, alertMessage: '', alertType: '' }"
    x-init='
        @if ($errors->any())
            showAlert = true;
            alertMessage = @json($errors->first());
            alertType = "error";
        @elseif(session("success"))
            showAlert = true;
            alertMessage = @json(session("success"));
            alertType = "success";
        @elseif(session("error"))
            showAlert = true;
            alertMessage = @json(session("error"));
            alertType = "error";
        @endif

        if (showAlert) {
            setTimeout(() => showAlert = false, 4000)
        }
    '
>

    <template x-if="showAlert">
        <div 
            x-transition
            class="mb-4 rounded-lg p-4 flex justify-between items-center"
            :class="{
                'bg-green-50 border border-green-200 text-green-700': alertType === 'success',
                'bg-red-50 border border-red-200 text-red-700': alertType === 'error'
            }"
        >
            <span x-text="alertMessage"></span>

            <button @click="showAlert = false" class="ml-4 text-sm font-bold">
                ✕
            </button>
        </div>
    </template>

</div>