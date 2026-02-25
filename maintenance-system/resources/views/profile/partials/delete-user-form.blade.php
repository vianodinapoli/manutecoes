<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Eliminar Conta') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
Assim que a sua conta for eliminada, todos os seus recursos e dados serão apagados permanentemente. Antes de eliminar a sua conta, por favor, descarregue quaisquer dados ou informações que deseje reter.        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >{{ __('Eliminar Conta') }}</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Tem a certeza que pretende eliminar a sua conta?') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __('Depois que a sua conta for eliminada, todos os seus recursos e dados serão eliminados permanentemente. Digite a sua palavra-passe para confirmar que deseja eliminar permanentemente a sua conta.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="{{ __('Password') }}"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancelar') }}
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    {{ __('Eliminar Conta') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
