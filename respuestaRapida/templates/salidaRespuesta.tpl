        <table width="70%" border="0" align="center" margin="4" CELLPADDING="10" cellspacing="0" class="table" >

            <tr bordercolor="#FFFFFF">
                <td colspan="2" height="40" align="center" class="titulos4"
                    valign="middle">
                    <b><span class=etexto>Respuesta Rapida</span></b>
                </td>
            </tr>
            <!--{if $noerror ge 1 or $salida eq 'ok'}-->
                <tr>
                    <td <!--{if !$sali}--> colspan="2" <!--{/if}--> valign="middle">
                        <b><span class=etexto>Radicado de respuesta  No. <!--{$nurad}--></span></b>
                    </td>
                    <!--{if $sali}-->
                    <td valign="middle">
                        <b><span class=etexto>Anexos de la respuesta </span></b>
                            <!--{section name=customer loop=$sali}-->
                                <li><a href="<!--{$sali[customer].path}-->"><!--{$sali[customer].desc}--></a></li>
                            <!--{/section}-->
                    </td>
                    <!--{/if}--> 
                </tr>

                <tr>
                    <td colspan="2" >
                        <b><span class=etexto><!--{$error}--></span></b>
                    </td>
                </tr>
            <!--{else}-->
                <tr>
                    <td colspan="2" >
                        <b><span class=etexto><!--{$error}--></span></b>
                    </td>
                </tr>
            <!--{/if}-->
        </table>
        <!--{if $noerror ge 1 or $salida eq 'ok'}-->
        <iframe src="../radicacion/tipificar_documento.php?rr=xyz&<!--{$sid}-->&nurad=<!--{$nurad}-->&dependencia=<!--{$dependencia}-->&krd=<!--{$krd}-->&tsub=0&codserie=0&respuesta_rap=<!--{$respuesta_rap}-->" 
        width='100%' height='540px' style='border: 0px'>
        </iframe>
        <!--{/if}-->
        <center>
          <footer>    <input name="Cerrar" type="button" class="btn btn-default" id="envia22" onClick="window.parent.opener.$.fn.cargarPagina('./lista_anexos.php', 'tabs-c'); window.parent.close();" value=" Cerrar "></footer>
          
        </center>
