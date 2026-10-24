@props([
    'name',
    'value' => '',
    'placeholder' => '0',
    'required' => false,
])

@php
    $initial = ($value === null || $value === '') ? '' : (int) round((float) $value);
@endphp

{{-- Input nominal Rupiah dengan separator ribuan; nilai angka murni dikirim lewat hidden input --}}
<div class="relative"
     x-data="{
        amount: @js($initial),
        format(num) {
            return (num === '' || num === null) ? '' : new Intl.NumberFormat('id-ID').format(num);
        },
        mask(e) {
            const el = e.target;
            const digitsBeforeCaret = el.value.slice(0, el.selectionStart).replace(/\D/g, '').length;
            const digits = el.value.replace(/\D/g, '').replace(/^0+(?=\d)/, '');
            this.amount = digits === '' ? '' : parseInt(digits, 10);
            const formatted = this.format(this.amount);
            el.value = formatted;
            let pos = 0, seen = 0;
            while (pos < formatted.length && seen < digitsBeforeCaret) {
                if (/\d/.test(formatted[pos])) seen++;
                pos++;
            }
            el.setSelectionRange(pos, pos);
        }
     }">
    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 font-bold pointer-events-none">Rp</span>
    <input type="text" inputmode="numeric" autocomplete="off"
           :value="format(amount)"
           @input="mask($event)"
           placeholder="{{ $placeholder }}"
           @if($required) required @endif
           {{ $attributes->merge(['class' => 'w-full pl-9 pr-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500']) }}>
    <input type="hidden" name="{{ $name }}" :value="amount">
</div>
