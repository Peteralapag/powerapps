(function(){
  // showToast(type, message, durationMs)
  window.showToast = function(type, message, duration){
    duration = duration || 2000;
    var containerId = 'rms-toast-container';
    var container = document.getElementById(containerId);
    if(!container){
      container = document.createElement('div');
      container.id = containerId;
      container.style.position = 'fixed';
      container.style.top = '20px';
      container.style.right = '20px';
      container.style.zIndex = 999999;
      document.body.appendChild(container);
    }
    var toast = document.createElement('div');
    toast.className = 'rms-toast rms-toast-' + (type || 'success');
    toast.style.marginTop = '8px';
    toast.style.padding = '10px 14px';
    toast.style.borderRadius = '4px';
    toast.style.color = '#fff';
    toast.style.minWidth = '180px';
    toast.style.boxShadow = '0 2px 6px rgba(0,0,0,0.2)';
    toast.style.fontFamily = 'Arial, sans-serif';
    toast.style.opacity = '0';
    toast.style.transition = 'opacity 200ms ease, transform 200ms ease';
    toast.style.transform = 'translateY(-6px)';
    if(type === 'error') toast.style.background = '#e74c3c';
    else if(type === 'warning') toast.style.background = '#f39c12';
    else if(type === 'info') toast.style.background = '#3498db';
    else toast.style.background = '#27ae60';
    toast.innerText = message || '';
    container.appendChild(toast);
    // animate in
    requestAnimationFrame(function(){ toast.style.opacity = '1'; toast.style.transform = 'translateY(0)'; });
    // remove
    setTimeout(function(){
      toast.style.opacity = '0';
      toast.style.transform = 'translateY(-6px)';
      setTimeout(function(){ try{ container.removeChild(toast); }catch(e){} }, 220);
    }, duration);
  };
})();
