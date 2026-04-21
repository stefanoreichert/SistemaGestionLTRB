<div style="display:grid;gap:1rem;">
    <div>
        <label style="font-size:.75rem;color:#9ca3af;display:block;margin-bottom:.3rem;">Nombre *</label>
        <input name="name" value="{{ old('name', $product->name ?? '') }}" required
               style="width:100%;box-sizing:border-box;background:#1e1e1e;border:1px solid #2a2a2a;
                      color:#e5e7eb;border-radius:.5rem;padding:.5rem .75rem;font-size:.82rem;">
        @error('name')<p style="color:#f87171;font-size:.72rem;margin-top:.2rem;">{{ $message }}</p>@enderror
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;">
        <div>
            <label style="font-size:.75rem;color:#9ca3af;display:block;margin-bottom:.3rem;">Tipo *</label>
            <select name="type" required
                    style="width:100%;background:#1e1e1e;border:1px solid #2a2a2a;color:#e5e7eb;
                           border-radius:.5rem;padding:.5rem .75rem;font-size:.82rem;">
                <option value="">— Seleccionar —</option>
                <option value="Comida" {{ old('type', $product->type ?? '') === 'Comida' ? 'selected' : '' }}>Comida</option>
                <option value="Bebida" {{ old('type', $product->type ?? '') === 'Bebida' ? 'selected' : '' }}>Bebida</option>
            </select>
            @error('type')<p style="color:#f87171;font-size:.72rem;margin-top:.2rem;">{{ $message }}</p>@enderror
        </div>
        <div>
            <label style="font-size:.75rem;color:#9ca3af;display:block;margin-bottom:.3rem;">Categoría *</label>
            <input name="category" value="{{ old('category', $product->category ?? '') }}" required
                   style="width:100%;box-sizing:border-box;background:#1e1e1e;border:1px solid #2a2a2a;
                          color:#e5e7eb;border-radius:.5rem;padding:.5rem .75rem;font-size:.82rem;">
            @error('category')<p style="color:#f87171;font-size:.72rem;margin-top:.2rem;">{{ $message }}</p>@enderror
        </div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;">
        <div>
            <label style="font-size:.75rem;color:#9ca3af;display:block;margin-bottom:.3rem;">Precio *</label>
            <input name="price" type="number" step="0.01" min="0"
                   value="{{ old('price', $product->price ?? '') }}" required
                   style="width:100%;box-sizing:border-box;background:#1e1e1e;border:1px solid #2a2a2a;
                          color:#e5e7eb;border-radius:.5rem;padding:.5rem .75rem;font-size:.82rem;">
            @error('price')<p style="color:#f87171;font-size:.72rem;margin-top:.2rem;">{{ $message }}</p>@enderror
        </div>
        <div>
            <label style="font-size:.75rem;color:#9ca3af;display:block;margin-bottom:.3rem;">Stock *</label>
            <input name="stock" type="number" min="0"
                   value="{{ old('stock', $product->stock ?? 0) }}" required
                   style="width:100%;box-sizing:border-box;background:#1e1e1e;border:1px solid #2a2a2a;
                          color:#e5e7eb;border-radius:.5rem;padding:.5rem .75rem;font-size:.82rem;">
            @error('stock')<p style="color:#f87171;font-size:.72rem;margin-top:.2rem;">{{ $message }}</p>@enderror
        </div>
    </div>
    <div style="display:flex;align-items:center;gap:.5rem;">
        <input type="hidden" name="active" value="0">
        <input type="checkbox" name="active" value="1" id="chk-active"
               {{ old('active', $product->active ?? true) ? 'checked' : '' }}
               style="width:1rem;height:1rem;accent-color:#d97706;">
        <label for="chk-active" style="font-size:.82rem;color:#e5e7eb;">Producto activo (visible en el menú)</label>
    </div>
</div>
