<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => "L'attribut :attribute doit être accepté.",
    'accepted_if' => "L'attribut :attribute doit être accepté lorsque :other est :value.",
    'active_url' => "L'attribut :attribute n'est pas une URL valide.",
    'after' => "L'attribut :attribute doit être une date postérieure à :date.",
    'after_or_equal' => "L'attribut :attribute doit être une date postérieure ou égale à :date.",
    'alpha' => "L'attribut :attribute ne doit contenir que des lettres.",
    'alpha_dash' => "L'attribut :attribute ne doit contenir que des lettres, des chiffres, des tirets et des traits de soulignement.",
    'alpha_num' => "L'attribut :attribute ne doit contenir que des lettres et des chiffres.",
    'array' => "L'attribut :attribute doit être un tableau.",
    'before' => "L'attribut :attribute doit être une date antérieure à :date.",
    'before_or_equal' => "L'attribut :attribute doit être une date antérieure ou égale à :date.",
    'between' => [
        'numeric' => "L'attribut :attribute doit être compris entre :min et :max.",
        'file' => "L'attribut :attribute doit être compris entre :min et :max kilo-octets.",
        'string' => "L'attribut :attribute doit être compris entre :min et :max caractères.",
        'array' => "L'attribut :attribute doit contenir entre :min et :max éléments.",
    ],
    'boolean' => "Le champ :attribute doit être vrai ou faux.",
    'confirmed' => "La confirmation :attribute ne correspond pas.",
    'current_password' => "Le mot de passe est incorrect.",
    'date' => "La date :attribute n'est pas valide.",
    'date_equals' => "La date :attribute doit être égale à :date.",
    'date_format' => "La valeur :attribute ne correspond pas au format :format.",
    'different' => "Les valeurs :attribute et :other doivent être différentes.",
    'digits' => "La valeur :attribute doit être de :digits.",
    'digits_between' => "La valeur :attribute doit être comprise entre :min et :max.",
    'dimensions' => "Les dimensions d'image de :attribute sont invalides.",
    'distinct' => "Le champ :attribute contient un doublon. value.",
    'email' => "L'attribut :attribute doit être une adresse e-mail valide.",
    'ends_with' => "L'attribut :attribute doit se terminer par l'une des valeurs suivantes : :values.",
    'exists' => "L'attribut :attribute sélectionné n'est pas valide.",
    'file' => "L'attribut :attribute doit être un fichier.",
    'filled' => "Le champ :attribute doit contenir une valeur.",
    'gt' => [
        'numeric' => "L'attribut :attribute doit être supérieur à :value.",
        'file' => "L'attribut :attribute doit être supérieur à :value kilo-octets.",
        'string' => "L'attribut :attribute doit être supérieur à :value caractères.",
        'array' => "L'attribut :attribute doit contenir plus de :value éléments.",
    ],
    'gte' => [
        'numeric' => "L'attribut :attribute doit être supérieur ou égal à :value.",
        'file' => "L'attribut :attribute doit être supérieur ou égal à :value kilo-octets.",
        'string' => "L'attribut :attribute doit être supérieur ou égal à :value caractères.",
        'array' => "L'attribut :attribute doit contenir au moins :value éléments.",
    ],
    'image' => "L'attribut :attribute doit être une image.",
    'in' => "L'attribut :attribute sélectionné n'est pas valide.",
    'in_array' => "Le champ :attribute n'existe pas dans :other.",
    'integer' => "L'attribut :attribute doit être un entier.",
    'ip' => "L'attribut :attribute doit être une adresse IP valide.",
    'ipv4' => "L'attribut :attribute doit être une adresse IPv4 valide.",
    'ipv6' => "L'attribut :attribute doit être une adresse IPv6 valide.",
    'json' => "L'attribut :attribute doit être une chaîne JSON valide.",
    'lt' => [
        'numeric' => "L'attribut :attribute doit être inférieur à :value.",
        'file' => "L'attribut :attribute doit être inférieur à :value kilo-octets.",
        'string' => "L'attribut :attribute doit être inférieur à :value caractères.",
        'array' => "L'attribut :attribute doit contenir moins de :value éléments.",
    ],
    'lte' => [
        'numeric' => "L'attribut :attribute doit être inférieur ou égal à :value.",
        'file' => "L'attribut :attribute doit contenir moins de :value kilo-octets.",
        'string' => "L'attribut :attribute doit contenir moins de :value caractères.",
        'array' => "L'attribut :attribute ne doit pas contenir plus de :value éléments.",
    ],
    'max' => [
        'numeric' => "L'attribut :attribute ne doit pas être supérieur à :max.",
        'file' => "L'attribut :attribute ne doit pas être supérieur à :max kilo-octets.",
        'string' => "L'attribut :attribute ne doit pas être supérieur à :max caractères.",
        'array' => "L'attribut :attribute ne doit pas contenir plus de :max éléments.",
    ],
    'mimes' => "L'attribut :attribute doit être un fichier de type : :values.",
    'mimetypes' => "L'attribut :attribute doit être un fichier de type : :values.",
    'min' => [
        'numeric' => "L'attribut :attribute doit contenir au moins :min.",
        'file' => "L'attribut :attribute doit contenir au moins :min kilo-octets.",
        'string' => "L'attribut :attribute doit contenir au moins :min caractères.",
        'array' => "L' :attribute doit contenir au moins :min éléments.",
    ],
    'multiple_of' => "L'attribut :attribute doit être un multiple de :value.",
    'not_in' => "L'attribut :attribute sélectionné n'est pas valide.",
    'not_regex' => "Le format de l'attribut :attribute n'est pas valide.",
    'numeric' => "L'attribut :attribute doit être un nombre.",
    'password' => "Le mot de passe est incorrect.",
    'present' => "Le champ :attribute doit être présent.",
    'regex' => "Le format de l'attribut :attribute n'est pas valide.",
    'required' => "Le champ :attribute est obligatoire.",
    'required_if' => "Le champ :attribute est obligatoire lorsque :other est :value.",
    'required_unless' => "Le champ :attribute est obligatoire sauf si :other est dans :values.",
    'required_with' => "Le champ :attribute est obligatoire lorsque :values ​​est present.",
    'required_with_all' => "Le champ :attribute est obligatoire lorsque :values ​​est présent.",
    'required_without' => "Le champ :attribute est obligatoire lorsque :values ​​n'est pas présent.",
    'required_without_all' => "Le champ :attribute est obligatoire lorsqu'aucun des :values ​​n'est présent.",
    'prohibited' => "Le champ :attribute est interdit.",
    'prohibited_if' => "Le champ :attribute est interdit lorsque :other est :value.",
    'prohibited_unless' => "Le champ :attribute est interdit sauf si :other est dans :values.",
    'prohibits' => "Le champ :attribute interdit la présence de :other.",
    'same' => "Les champs :attribute et :other doivent correspondre.",
    'size' => [
        'numeric' => "Le champ :attribute doit être :size.",
        'file' => "L'attribut :attribute doit être de :size kilo-octets.",
        'string' => "L'attribut :attribute doit être de :size caractères.",
        'array' => "L'attribut :attribute doit contenir :size éléments.",
    ],
    'starts_with' => "L'attribut :attribute doit commencer par l'un des éléments suivants : :values.",
    'string' => "L'attribut :attribute doit être une chaîne.",
    'timezone' => "L'attribut :attribute doit être un fuseau horaire valide.",
    'unique' => "L'attribut :attribute a déjà été utilisé.",
    'uploaded' => "L'attribut :attribute n'a pas pu être téléchargé.",
    'url' => "L'attribut :attribute doit être une URL valide.",
    'uuid' => "L'attribut :attribute doit être un UUID valide.",

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => "custom-message",
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
