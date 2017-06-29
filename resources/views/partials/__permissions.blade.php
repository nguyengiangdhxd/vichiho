@if($can_edit)
    <button class="btn btn-danger pull-right text-uppercase" id="_save-permission">Lưu phân quyền</button>
@endif

@if(!empty($permissions))
    <ul class="list-unstyled">
        @foreach($permissions as $key => $permission)
            <li><h4>{{$permission['label']}}</h4></li>

            @if(!empty($permission['permissions']))
                <ul style="list-style: none;">
                    @foreach($permission['permissions'] as $k => $permission_item)
                        <li data-placement="left" data-toggle="tooltip" title="{{$permission_item['description']}}">
                            @if($can_edit)
                                <div class="checkbox checkbox-inline">
                                    <input id="checkbox_{{$k}}" @if(in_array($k, $permissions_role)) checked @endif type="checkbox" class="_set-permission" value="{{$k}}">
                                    <label for="checkbox_{{$k}}">
                                        <span>{{$permission_item['label']}}</span>
                                    </label>
                                </div>
                            @else
                                <span>{{$permission_item['label']}}</span>
                            @endif

                        </li>
                    @endforeach
                </ul>
            @endif

        @endforeach
    </ul>
@endif