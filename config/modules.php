<?php

return [
    'condominio' => [
        'label' => 'Condomínio',
        'icon' => '🏢',
        'modulos' => [
            'campo' => true,
            'pessoas' => true,
            'financeiro' => true,
            'chamados' => true,
            'reservas' => true,
            'portaria' => true,
        ],
        'pessoas' => [
            'tipos' => ['morador', 'proprietario', 'sindico', 'porteiro'],
            'label_singular' => 'Morador',
            'label_plural' => 'Moradores',
        ],
        'estrutura' => [
            'nivel1' => 'Bloco',
            'nivel2' => 'Pavimento',
            'nivel3' => 'Unidade',
        ],
    ],
    
    'clinica' => [
        'label' => 'Clínica',
        'icon' => '🏥',
        'modulos' => [
            'campo' => true,
            'pessoas' => true,
            'financeiro' => true,
            'agendamentos' => true,
            'chamados' => false,
            'reservas' => false,
        ],
        'pessoas' => [
            'tipos' => ['paciente', 'medico', 'enfermeiro', 'recepcionista'],
            'label_singular' => 'Paciente',
            'label_plural' => 'Pacientes',
        ],
        'estrutura' => [
            'nivel1' => 'Setor',
            'nivel2' => 'Sala',
            'nivel3' => 'Equipamento',
        ],
    ],
    
    'empresa' => [
        'label' => 'Empresa',
        'icon' => '🏭',
        'modulos' => [
            'campo' => true,
            'pessoas' => true,
            'financeiro' => true,
            'chamados' => true,
            'reservas' => true,
            'portaria' => false,
        ],
        'pessoas' => [
            'tipos' => ['colaborador', 'gerente', 'diretor', 'estagiario'],
            'label_singular' => 'Colaborador',
            'label_plural' => 'Colaboradores',
        ],
        'estrutura' => [
            'nivel1' => 'Filial',
            'nivel2' => 'Departamento',
            'nivel3' => 'Sala',
        ],
    ],
    
    'igreja' => [
        'label' => 'Igreja',
        'icon' => '⛪',
        'modulos' => [
            'campo' => false,
            'pessoas' => true,
            'financeiro' => true,
            'chamados' => false,
            'reservas' => true,
            'comunicacao' => true,
        ],
        'pessoas' => [
            'tipos' => ['membro', 'lider', 'pastor', 'diacono'],
            'label_singular' => 'Membro',
            'label_plural' => 'Membros',
        ],
        'estrutura' => [
            'nivel1' => 'Sede',
            'nivel2' => 'Congregação',
            'nivel3' => 'Sala',
        ],
    ],
];