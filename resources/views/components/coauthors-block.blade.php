@php
    $help = $helpText
        ?? __('public.write_coauthors_article');
@endphp

<div
    class="bg-white p-6 rounded-xl shadow-sm space-y-4"
    x-data="{
        coauthors: @json($initial ?: []),
        add() {
            this.coauthors.push({
                role: '',
                fullname: '',
                organisation: '',
                orcid: '',
            });
        },
        remove(index) {
            this.coauthors.splice(index, 1);
        }
    }"
>
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-custom-main">
            {{ $title }}
        </h2>

        <button
            type="button"
            @click="add()"
            class="inline-flex items-center px-3 py-2 border border-custom-main text-xs font-semibold rounded-lg text-custom-main hover:bg-custom-main hover:text-white transition-colors"
        >
            + {{ __('public.add_coauthor') }}
        </button>
    </div>

    <p class="text-xs text-gray-500">
        {{ $help }}
    </p>

    {{-- Сообщение, если соавторов нет --}}
    <template x-if="coauthors.length === 0">
        <p class="text-sm text-gray-400 mt-2">
            {{ __('public.no_coauthors') }}
        </p>
    </template>

    {{-- Список соавторов --}}
    <template x-for="(coauthor, index) in coauthors" :key="index">
        <div class="border rounded-lg p-4 space-y-4 mt-4">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700"
                    x-text="`{{ __('public.coauthor') }} #${index + 1}`"></h3>

                <button
                    type="button"
                    @click="remove(index)"
                    class="text-xs text-red-600 hover:text-red-800"
                >
                    {{ __('public.delete') }}
                </button>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                {{-- ФИО --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('auth.fullname') }}
                    </label>
                    <input
                        type="text"
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-[15px] py-[10px]"
                        placeholder="..."
                        x-model="coauthor.fullname"
                        :name="`coauthors[${index}][fullname]`"
                    >
                </div>

                {{-- Организация --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('auth.organisation') }}
                    </label>
                    <input
                        type="text"
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-[15px] py-[10px]"
                        placeholder="..."
                        x-model="coauthor.organisation"
                        :name="`coauthors[${index}][organisation]`"
                    >
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                {{-- Роль --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('public.role') }}
                    </label>
                    <select
                        class="block w-full border rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-[15px] py-[10px]"
                        x-model="coauthor.role"
                        :name="`coauthors[${index}][role]`"
                    >
                        <option value="">{{ __('public.select_role') }}</option>
                        <option value="coauthor">{{ __('public.coauthor') }}</option>
                        <option value="translator">{{ __('public.translator') }}</option>
                        <option value="editor">{{ __('public.editor') }}</option>
                        <option value="reviewer">{{ __('public.reviewer') }}</option>
                    </select>
                </div>

                {{-- ORCID --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        ORCID
                    </label>
                    <input
                        type="text"
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-[15px] py-[10px]"
                        placeholder="https://"
                        x-model="coauthor.orcid"
                        :name="`coauthors[${index}][orcid]`"
                    >
                </div>
            </div>
        </div>
    </template>

    @error('coauthors')
    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>
