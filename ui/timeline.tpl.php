<h2>{{@lng.timeline}}</h2>

<table class="table-striped table">
    <thead>
        <tr>
            <th>{{@lng.description}}</th>
            <th>{{@lng.changed}}</th>
            <th>{{@lng.changedby}}</th>
        </tr>
    </thead>
    <tbody>
    <repeat group="{{@activities}}" value="{{@activity}}">
        <tr>
            <td>{{@activity.description}}</td>
            <td>{{date('d.m.Y H:i', @activity.changed)}}</td>
            <td><a href="/user/{{@activity->username}}">{{@activity.username}}</a></td>
        </tr>
    </repeat>
    </tbody>
</table>
