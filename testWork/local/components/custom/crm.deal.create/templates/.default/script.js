function createDeal() {
    const form = document.getElementById('deal_create_form');
    const inputs = form.querySelectorAll('input');
    let fields = {};
    inputs.forEach(input => {
        if (input.value) {
            fields[input.getAttribute('name')] = input.value;
        }
        console.log(input.value);
        console.log(input.getAttribute('name'));

    });

    BX.ajax.runComponentAction('custom:crm.deal.create', 'createDeal', {
        mode: 'class',
        data: {
            fields: fields
        },
    }).then(
        function (response)
        {
            console.log(response);
            if (response.status == 'success') {
                window.location.href = 'crm/deal/details/'+response.data.newId+'/';
                //location.reload();
            }
        },
        function (response)
        {
            console.log(response);
        }
    );
}

BX.ready(function () {
    document.querySelector('#create_deal').addEventListener('click', createDeal);
});