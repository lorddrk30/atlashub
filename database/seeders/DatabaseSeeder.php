<?php

namespace Database\Seeders;

use App\Models\Artefact;
use App\Models\Endpoint;
use App\Models\Module;
use App\Models\OrganizationSetting;
use App\Models\System;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissionNames = [
            'system.manage',
            'module.manage',
            'endpoint.manage',
            'endpoint.publish',
            'artefact.manage',
            'organization.manage',
            'user.manage',
            'role.manage',
            'logs.view',
            'logs.manage',
        ];

        foreach ($permissionNames as $permissionName) {
            Permission::query()->firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
        }

        $adminRole = Role::query()->firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $editorRole = Role::query()->firstOrCreate(['name' => 'editor', 'guard_name' => 'web']);
        $viewerRole = Role::query()->firstOrCreate(['name' => 'viewer', 'guard_name' => 'web']);

        $adminRole->syncPermissions($permissionNames);
        $editorRole->syncPermissions([
            'system.manage',
            'module.manage',
            'endpoint.manage',
            'endpoint.publish',
            'artefact.manage',
            'logs.view',
        ]);
        $viewerRole->syncPermissions([]);

        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@rikarcoffe.local'],
            ['name' => 'Admin RikarCoffe', 'password' => Hash::make('password')]
        );

        $editor = User::query()->firstOrCreate(
            ['email' => 'editor@rikarcoffe.local'],
            ['name' => 'Editor RikarCoffe', 'password' => Hash::make('password')]
        );

        $viewer = User::query()->firstOrCreate(
            ['email' => 'viewer@rikarcoffe.local'],
            ['name' => 'Viewer RikarCoffe', 'password' => Hash::make('password')]
        );

        $admin->syncRoles([$adminRole]);
        $editor->syncRoles([$editorRole]);
        $viewer->syncRoles([$viewerRole]);

        OrganizationSetting::query()->updateOrCreate(
            ['slug' => config('organization.default_slug', 'rikarcoffe')],
            [
                'name' => config('organization.default_name', 'RikarCoffe'),
                'short_name' => config('organization.default_short_name', 'RC'),
                'tagline' => config('organization.default_tagline', 'Portal interno para APIs y artefactos tecnicos'),
                'description' => config('organization.default_description', 'Catalogo interno para sistemas, modulos, endpoints y artefactos de RikarCoffe.'),
                'logo' => null,
                'favicon' => null,
                'support_email' => config('organization.default_support_email', 'tech@rikarcoffe.local'),
                'primary_color' => config('organization.default_primary_color', '#22d3ee'),
                'secondary_color' => config('organization.default_secondary_color', '#34d399'),
            ]
        );

        $customerExperience = System::query()->firstOrCreate(
            ['slug' => 'customer-experience'],
            ['name' => 'Customer Experience', 'description' => 'Pedidos web, app y kioskos de RikarCoffe.']
        );

        $storeOperations = System::query()->firstOrCreate(
            ['slug' => 'store-operations'],
            ['name' => 'Store Operations', 'description' => 'Operacion diaria en sucursales y turnos de baristas.']
        );

        $supplyChain = System::query()->firstOrCreate(
            ['slug' => 'supply-chain'],
            ['name' => 'Supply Chain', 'description' => 'Compras, proveedores e inventario de insumos.']
        );

        $ordersModule = Module::query()->firstOrCreate(
            ['system_id' => $customerExperience->id, 'slug' => 'orders'],
            ['name' => 'Orders', 'description' => 'Flujo de ordenes de cafe y alimentos.', 'status' => 'active']
        );

        $menuModule = Module::query()->firstOrCreate(
            ['system_id' => $customerExperience->id, 'slug' => 'menu-catalog'],
            ['name' => 'Menu Catalog', 'description' => 'Catalogo de bebidas y productos.', 'status' => 'active']
        );

        $inventoryModule = Module::query()->firstOrCreate(
            ['system_id' => $storeOperations->id, 'slug' => 'inventory-control'],
            ['name' => 'Inventory Control', 'description' => 'Ajustes de stock por sucursal.', 'status' => 'active']
        );

        $shiftsModule = Module::query()->firstOrCreate(
            ['system_id' => $storeOperations->id, 'slug' => 'barista-shifts'],
            ['name' => 'Barista Shifts', 'description' => 'Turnos y cobertura de personal.', 'status' => 'active']
        );

        $suppliersModule = Module::query()->firstOrCreate(
            ['system_id' => $supplyChain->id, 'slug' => 'supplier-orders'],
            ['name' => 'Supplier Orders', 'description' => 'Ordenes de compra a proveedores.', 'status' => 'active']
        );

        $createOrderEndpoint = Endpoint::query()->firstOrCreate(
            ['module_id' => $ordersModule->id, 'method' => 'POST', 'path' => '/api/v1/orders'],
            [
                'name' => 'Create order',
                'description' => 'Crea una orden para retiro en tienda o entrega.',
                'parameters' => [
                    ['name' => 'store_id', 'in' => 'body', 'required' => true, 'type' => 'string', 'description' => 'Sucursal destino'],
                    ['name' => 'items', 'in' => 'body', 'required' => true, 'type' => 'array', 'description' => 'Listado de productos'],
                ],
                'request_example' => [
                    'store_id' => 'CENTRO-01',
                    'items' => [
                        ['sku' => 'LATTE-GRANDE', 'qty' => 1],
                        ['sku' => 'CROISSANT-MANTEQUILLA', 'qty' => 2],
                    ],
                ],
                'response_example' => [
                    'order_id' => 'RC-ORDER-91231',
                    'status' => 'accepted',
                    'eta_minutes' => 14,
                ],
                'authentication_type' => 'bearer',
                'urls' => [
                    'prod' => 'https://api.rikarcoffe.local/api/v1/orders',
                    'uat' => 'https://api-uat.rikarcoffe.local/api/v1/orders',
                    'dev' => 'https://api-dev.rikarcoffe.local/api/v1/orders',
                ],
                'status' => 'published',
                'created_by' => $admin->id,
                'updated_by' => $admin->id,
            ]
        );

        $menuItemsEndpoint = Endpoint::query()->firstOrCreate(
            ['module_id' => $menuModule->id, 'method' => 'GET', 'path' => '/api/v1/menu/items'],
            [
                'name' => 'List menu items',
                'description' => 'Obtiene el menu vigente por sucursal y horario.',
                'parameters' => [
                    ['name' => 'store_id', 'in' => 'query', 'required' => true, 'type' => 'string', 'description' => 'Sucursal solicitada'],
                    ['name' => 'channel', 'in' => 'query', 'required' => false, 'type' => 'string', 'description' => 'app, web o kiosk'],
                ],
                'request_example' => [
                    'query' => [
                        'store_id' => 'CENTRO-01',
                        'channel' => 'app',
                    ],
                ],
                'response_example' => [
                    'items' => [
                        ['sku' => 'AMERICANO-M', 'name' => 'Americano Mediano', 'price' => 49.00],
                        ['sku' => 'MATCHA-LATTE', 'name' => 'Matcha Latte', 'price' => 82.00],
                    ],
                ],
                'authentication_type' => 'api_key',
                'urls' => [
                    'prod' => 'https://api.rikarcoffe.local/api/v1/menu/items',
                    'uat' => 'https://api-uat.rikarcoffe.local/api/v1/menu/items',
                    'dev' => 'https://api-dev.rikarcoffe.local/api/v1/menu/items',
                ],
                'status' => 'published',
                'created_by' => $admin->id,
                'updated_by' => $admin->id,
            ]
        );

        $inventoryAdjustmentEndpoint = Endpoint::query()->firstOrCreate(
            ['module_id' => $inventoryModule->id, 'method' => 'POST', 'path' => '/api/v1/inventory/adjustments'],
            [
                'name' => 'Register inventory adjustment',
                'description' => 'Registra ajustes de inventario por merma o recepcion.',
                'parameters' => [
                    ['name' => 'store_id', 'in' => 'body', 'required' => true, 'type' => 'string', 'description' => 'Sucursal'],
                    ['name' => 'sku', 'in' => 'body', 'required' => true, 'type' => 'string', 'description' => 'SKU del insumo'],
                    ['name' => 'delta', 'in' => 'body', 'required' => true, 'type' => 'number', 'description' => 'Variacion de stock'],
                ],
                'request_example' => [
                    'store_id' => 'CENTRO-01',
                    'sku' => 'LECHE-AVENA-1L',
                    'delta' => -3,
                    'reason' => 'merma',
                ],
                'response_example' => [
                    'adjustment_id' => 'INV-33210',
                    'status' => 'registered',
                ],
                'authentication_type' => 'oauth2',
                'urls' => [
                    'prod' => 'https://ops.rikarcoffe.local/api/v1/inventory/adjustments',
                    'uat' => 'https://ops-uat.rikarcoffe.local/api/v1/inventory/adjustments',
                    'dev' => 'https://ops-dev.rikarcoffe.local/api/v1/inventory/adjustments',
                ],
                'status' => 'published',
                'created_by' => $admin->id,
                'updated_by' => $admin->id,
            ]
        );

        $shiftEndpoint = Endpoint::query()->firstOrCreate(
            ['module_id' => $shiftsModule->id, 'method' => 'GET', 'path' => '/api/v1/shifts/{shift_id}'],
            [
                'name' => 'Get shift detail',
                'description' => 'Consulta detalle de turnos de baristas por identificador.',
                'parameters' => [
                    ['name' => 'shift_id', 'in' => 'path', 'required' => true, 'type' => 'string', 'description' => 'ID del turno'],
                ],
                'request_example' => [
                    'path' => '/api/v1/shifts/SHIFT-2026-01',
                ],
                'response_example' => [
                    'shift_id' => 'SHIFT-2026-01',
                    'store_id' => 'CENTRO-01',
                    'barista_count' => 5,
                    'status' => 'open',
                ],
                'authentication_type' => 'bearer',
                'urls' => [
                    'prod' => 'https://ops.rikarcoffe.local/api/v1/shifts/{shift_id}',
                    'uat' => 'https://ops-uat.rikarcoffe.local/api/v1/shifts/{shift_id}',
                    'dev' => 'https://ops-dev.rikarcoffe.local/api/v1/shifts/{shift_id}',
                ],
                'status' => 'published',
                'created_by' => $admin->id,
                'updated_by' => $admin->id,
            ]
        );

        Endpoint::query()->firstOrCreate(
            ['module_id' => $suppliersModule->id, 'method' => 'POST', 'path' => '/api/v1/suppliers/purchase-orders'],
            [
                'name' => 'Create purchase order',
                'description' => 'Genera orden de compra para proveedores de granos e insumos.',
                'parameters' => [
                    ['name' => 'supplier_id', 'in' => 'body', 'required' => true, 'type' => 'string', 'description' => 'Proveedor destino'],
                    ['name' => 'items', 'in' => 'body', 'required' => true, 'type' => 'array', 'description' => 'Productos solicitados'],
                ],
                'request_example' => [
                    'supplier_id' => 'SUP-BEAN-09',
                    'items' => [
                        ['sku' => 'BEAN-ESPRESSO-1KG', 'qty' => 20],
                    ],
                ],
                'response_example' => [
                    'po_id' => 'PO-99212',
                    'status' => 'draft',
                ],
                'authentication_type' => 'oauth2',
                'urls' => [
                    'prod' => 'https://supply.rikarcoffe.local/api/v1/suppliers/purchase-orders',
                    'uat' => 'https://supply-uat.rikarcoffe.local/api/v1/suppliers/purchase-orders',
                    'dev' => 'https://supply-dev.rikarcoffe.local/api/v1/suppliers/purchase-orders',
                ],
                'status' => 'published',
                'created_by' => $admin->id,
                'updated_by' => $admin->id,
            ]
        );

        Artefact::query()->firstOrCreate(
            ['endpoint_id' => $createOrderEndpoint->id, 'type' => 'swagger', 'url' => 'https://docs.rikarcoffe.local/orders/swagger'],
            [
                'system_id' => $customerExperience->id,
                'module_id' => $ordersModule->id,
                'title' => 'Swagger Orders API',
                'description' => 'Contrato OpenAPI del flujo de ordenes.',
                'metadata' => ['owner' => 'platform-api'],
                'created_by' => $admin->id,
                'updated_by' => $admin->id,
            ]
        );

        Artefact::query()->firstOrCreate(
            ['endpoint_id' => $menuItemsEndpoint->id, 'type' => 'postman', 'url' => 'https://postman.example.com/collections/rikarcoffe-menu'],
            [
                'system_id' => $customerExperience->id,
                'module_id' => $menuModule->id,
                'title' => 'Postman Menu Catalog',
                'description' => 'Coleccion de pruebas para menu y disponibilidad.',
                'metadata' => ['version' => 'v1'],
                'created_by' => $admin->id,
                'updated_by' => $admin->id,
            ]
        );

        Artefact::query()->firstOrCreate(
            ['module_id' => $ordersModule->id, 'type' => 'repo', 'url' => 'https://git.example.local/rikarcoffe/orders-service'],
            [
                'system_id' => $customerExperience->id,
                'title' => 'Repositorio orders-service',
                'description' => 'Servicio principal de ordenes de RikarCoffe.',
                'metadata' => ['default_branch' => 'main'],
                'created_by' => $admin->id,
                'updated_by' => $admin->id,
            ]
        );

        Artefact::query()->firstOrCreate(
            ['endpoint_id' => $inventoryAdjustmentEndpoint->id, 'type' => 'docs', 'url' => 'https://wiki.rikarcoffe.local/display/OPS/Inventory+Adjustments'],
            [
                'system_id' => $storeOperations->id,
                'module_id' => $inventoryModule->id,
                'title' => 'Guia operativa de inventario',
                'description' => 'Reglas operativas para ajustes de stock.',
                'metadata' => ['source' => 'internal-wiki'],
                'created_by' => $admin->id,
                'updated_by' => $admin->id,
            ]
        );

        Artefact::query()->firstOrCreate(
            ['endpoint_id' => $shiftEndpoint->id, 'type' => 'dashboard', 'url' => 'https://grafana.rikarcoffe.local/d/store-shifts'],
            [
                'system_id' => $storeOperations->id,
                'module_id' => $shiftsModule->id,
                'title' => 'Dashboard de turnos',
                'description' => 'Monitoreo de cobertura y saturacion por sucursal.',
                'metadata' => ['tool' => 'grafana'],
                'created_by' => $admin->id,
                'updated_by' => $admin->id,
            ]
        );
    }
}
