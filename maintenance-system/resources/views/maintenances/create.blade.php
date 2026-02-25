<select name="status" class="form-control">
    <option value="pendente" {{ $maintenance->status == 'pendente' ? 'selected' : '' }}>Pendente</option>
    <option value="em manutenção" {{ $maintenance->status == 'em manutenção' ? 'selected' : '' }}>Em Manutenção</option>
    <option value="concluída" {{ $maintenance->status == 'concluída' ? 'selected' : '' }}>Concluída</option>
</select>
