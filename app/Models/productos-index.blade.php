<?php
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Almacen;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination; //---> TRAIT
    public $buscar = '';
    public $categoria_id = '';
    public $almacen_id = '';

    public function with(): array
    {
        // Agregamos 'categoria' para precargarla y evitar consultas N+1
        $consulta = Producto::with(['cantidad.almacen', 'categoria']);

        if ($this->buscar) {
            // Especificamos 'productos.nombre' por seguridad al hacer el join
            $consulta->where('productos.nombre', 'like', '%' . $this->buscar . '%');
        }
        if ($this->categoria_id) {
            $consulta->where('productos.categoria_id', $this->categoria_id);
        }
        if ($this->almacen_id) {
            $consulta->join('stock__almacen', 'productos.id', '=', 'stock__almacen.producto_id')
                     ->where('stock__almacen.almacen_id', $this->almacen_id)
                     ->select('productos.*');
        }

        return [
            'productos' => $consulta->orderBy('productos.nombre')->paginate(10),
            'categorias' => Categoria::orderBy('nombre')->get(),
            'almacenes'  => Almacen::orderBy('nombre')->get(),
        ];
    }

    // Volver a la primera página cada vez que cambie $buscar, $categoria_id o $almacen_id
    public function updated($property)
    {
        if (in_array($property, ['buscar', 'categoria_id', 'almacen_id'])) {
            $this->resetPage();
        }
    }
};
?>

<div>
    <div class="mb-4">
        <input type="text" wire:model.live.debounce.400ms="buscar" placeholder="Buscar por nombre o código">
    </div>

    <table>
        @forelse ($productos as $producto)
            <tr>
                <td>{{ $producto->nombre }}</td>
                <td>{{ $producto->cantidad->first()?->stock ?? 0 }}</td>
                {{-- Operador ?-> evita el error si el producto no tiene categoría --}}
                <td>{{ $producto->categoria?->nombre ?? 'Sin categoría' }}</td>
                {{-- El almacén se obtiene a través de la relación cantidad --}}
                <td>{{ $producto->cantidad->first()?->almacen?->nombre ?? 'Sin almacén' }}</td>
            </tr>
        @empty
            <tr><td colspan="4">Sin datos</td></tr>
        @endforelse
    </table>

    {{ $productos->links() }}
</div>