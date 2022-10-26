<?php namespace App\Currency\Local;

class BRL extends LocalCurrency {

    function id(): string {
        return "local_brl";
    }

    function walletId(): string {
        return "brl";
    }

    function name(): string {
        return "R$";
    }

    function alias(): string {
        return "BRL";
    }

    function displayName(): string {
        return "R$";
    }

    function icon(): string {
        return "brl";
    }

    protected function options(): array {
        return [];
    }

}
