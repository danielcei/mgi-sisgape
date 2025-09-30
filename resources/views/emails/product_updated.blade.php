@if(!blank($insertNewProduct))
    <div
        style="max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f5f5f5; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h2 style="color: #333; text-align: center; margin-bottom: 20px;">NOVOS PRODUTOS</h2>

        <ul style="list-style: none; padding: 0;">
            @foreach($insertNewProduct as $product)
                <li style="background-color: #fff; padding: 10px; margin-bottom: 10px; border-radius: 4px; box-shadow: 0 1px 2px rgba(0,0,0,0.1);">
                    [{{$product->spc_code }}] {{$product->name }}
                </li>
            @endforeach
        </ul>

    </div>
@endif

@if(!blank($insertParameter))
    <br><br>
    <div
        style="max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f5f5f5; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h2 style="color: #333; text-align: center; margin-bottom: 20px;">NOVOS PARAMÊTOS</h2>
        <ul style="list-style: none; padding: 0;">
            @foreach($insertParameter as $parameter)
                <li style="background-color: #fff; padding: 10px; margin-bottom: 10px; border-radius: 4px; box-shadow: 0 1px 2px rgba(0,0,0,0.1);">
                    [{{$parameter->product->spc_code }}] {{$parameter->product->name}} - {{ $parameter->name  }}
                    ({{$parameter->spc_name}})
                </li>
            @endforeach
        </ul>

    </div>
@endif

@if(!blank($insertOptional))
    <br><br>
    <div
        style="max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f5f5f5; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h2 style="color: #333; text-align: center; margin-bottom: 20px;">NOVOS OPCIONAIS</h2>
        <ul style="list-style: none; padding: 0;">
            @foreach($insertOptional as $optional)
                <li style="background-color: #fff; padding: 10px; margin-bottom: 10px; border-radius: 4px; box-shadow: 0 1px 2px rgba(0,0,0,0.1);">
                    [{{$optional->product->spc_code }}] ({{$optional->spc_code }}) {{$optional->product->name}}
                    - {{ $optional->name  }}
                    ({{$optional->spc_name}})
                </li>
            @endforeach
        </ul>

    </div>
@endif

@if(!blank($insertSupplier))
    <br><br>
    <div
        style="max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f5f5f5; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h2 style="color: #333; text-align: center; margin-bottom: 20px;">NOVOS INSUMOS</h2>
        <ul style="list-style: none; padding: 0;">
            @foreach($insertSupplier as $supplie)
                <li style="background-color: #fff; padding: 10px; margin-bottom: 10px; border-radius: 4px; box-shadow: 0 1px 2px rgba(0,0,0,0.1);">
                    [{{$supplie->product->spc_code }}] {{$supplie->product->name}} - ({{$supplie->spc_code }}
                    ) {{ $supplie->name  }}
                    ({{$supplie->spc_name}})
                </li>
            @endforeach
        </ul>

    </div>
@endif


@if(count($productDeleted) > 0)
    <div
        style="max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f5f5f5; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h2 style="color: darkred; text-align: center; margin-bottom: 20px;">PRODUTOS REMOVIDOS</h2>

        <ul style="list-style: none; padding: 0;">
            @foreach($productDeleted as $product)
                <li style="background-color: #fff; padding: 10px; margin-bottom: 10px; border-radius: 4px; box-shadow: 0 1px 2px rgba(0,0,0,0.1);">
                    [{{$product->spc_code }}] {{$product->name }}
                </li>
            @endforeach
        </ul>

    </div>
@endif

@if(count($parameterDeleted) > 0)
    <br><br>
    <div
        style="max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f5f5f5; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h2 style="color: darkred; text-align: center; margin-bottom: 20px;">PARAMÊTOS REMOVIDOS</h2>
        <ul style="list-style: none; padding: 0;">
            @foreach($parameterDeleted as $parameter)
                    <li style="background-color: #fff; padding: 10px; margin-bottom: 10px; border-radius: 4px; box-shadow: 0 1px 2px rgba(0,0,0,0.1);">
                        [{{$parameter->product->spc_code }}] {{$parameter->product->name}} - {{ $parameter->name  }}
                        ({{$parameter->spc_name}})
                    </li>
            @endforeach
        </ul>

    </div>
@endif

@if(count($optionalDeleted) > 0)
    <br><br>
    <div
        style="max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f5f5f5; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h2 style="color: darkred; text-align: center; margin-bottom: 20px;">OPCIONAIS REMOVIDOS</h2>
        <ul style="list-style: none; padding: 0;">
            @foreach($optionalDeleted as $optional)
                <li style="background-color: #fff; padding: 10px; margin-bottom: 10px; border-radius: 4px; box-shadow: 0 1px 2px rgba(0,0,0,0.1);">
                    [{{$optional->product->spc_code }}  ] {{$optional->product->name}} ({{$optional->spc_code }})
                    - {{ $optional->name  }}
                    ({{$optional->spc_name}})
                </li>
            @endforeach
        </ul>

    </div>
@endif
@if(count($supplierDeleted) > 0)
    <br><br>
    <div
        style="max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f5f5f5; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h2 style="color: darkred; text-align: center; margin-bottom: 20px;">INSUMOS REMOVIDOS</h2>
        <ul style="list-style: none; padding: 0;">
            @foreach($supplierDeleted as $supplie)
                <li style="background-color: #fff; padding: 10px; margin-bottom: 10px; border-radius: 4px; box-shadow: 0 1px 2px rgba(0,0,0,0.1);">
                    [{{$supplie->product->spc_code }}] {{$supplie->product->name}} - ({{$supplie->spc_code }}
                    ) {{ $supplie->name  }}
                    ({{$supplie->spc_name}})
                </li>
            @endforeach
        </ul>
    </div>
@endif
