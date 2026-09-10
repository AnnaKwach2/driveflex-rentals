(function(root){'use strict';
const money=value=>'KSh '+Number(value).toLocaleString('en-KE',{maximumFractionDigits:0});
function dateString(d=new Date()){const copy=new Date(d);copy.setMinutes(copy.getMinutes()-copy.getTimezoneOffset());return copy.toISOString().slice(0,10);}
function addDays(value,n){const d=new Date(value+'T12:00:00');d.setDate(d.getDate()+n);return dateString(d);}
function estimate({start,end,startTime='10:00',endTime='10:00',rate,offer='',minimum=3,today=dateString()}){
 if(!/^\d{4}-\d{2}-\d{2}$/.test(start||'')||!/^\d{4}-\d{2}-\d{2}$/.test(end||''))throw Error('Choose your pick-up and drop-off dates.');
 if(!/^([01]\d|2[0-3]):[0-5]\d$/.test(startTime)||!/^([01]\d|2[0-3]):[0-5]\d$/.test(endTime))throw Error('Choose valid pick-up and drop-off times.');
 if(start<today)throw Error('Pick-up must be today or a later date.');
 const ms=Date.parse(end+'T'+endTime+':00Z')-Date.parse(start+'T'+startTime+':00Z');
 if(!Number.isFinite(ms)||ms<=0)throw Error('Drop-off must be after pick-up.');
 if(ms<minimum*86400000)throw Error('Please choose a rental period of at least '+minimum+' full days.');
 if(!Number.isFinite(rate)||rate<=0)throw Error('A valid daily rate is required.');
 const days=Math.ceil(ms/86400000);let percent=0;
 if(offer==='weekend')percent=20;
 if(offer==='longterm'&&days>=30)percent=30;
 const subtotal=rate*days,discount=Math.round(subtotal*percent/100);
 return {days,subtotal,discount,percent,total:subtotal-discount};
}
const api={money,dateString,addDays,estimate};if(typeof module!=='undefined'&&module.exports)module.exports=api;else root.Rental=api;
})(typeof globalThis!=='undefined'?globalThis:this);
