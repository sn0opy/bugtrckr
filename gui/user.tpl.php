<h2>{@lng.user}: {@user.name}</h2>

<h3>{@lng.informations}</h3>
<div class="formRow">
    <div class="formLabel">{@lng.name}: </div>
    <div class="formValue">{@user.name}</div>
</div>

<h3>{@lng.tickets}:</h3>
{* available with -b6 of F3 *}
<F3:repeat group="{@tickets}" value="{@ticket}">
    {@ticket.owner}
</F3:repeat>

