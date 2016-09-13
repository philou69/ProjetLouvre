$('#datepicker1').datepicker({
  todayBtn:'true',
  format:'dd-mm-yyyy',
  autoclose:'true',
  pickerPosition: 'bottom-left',
  minView: 'month',
  language: 'fr',
  daysOfWeekDisabled:'0,2',
  startDate: '-0m',
  datesDisabled: [{% for date in listDate %} '{{ date.dateReservation|date('d-m-Y') }}'{% if loop.last%} {% else %},{% endif %}
  {% endfor %}]
});
